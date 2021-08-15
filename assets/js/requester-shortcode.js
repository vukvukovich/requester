function ready(fn) {
	if (document.readyState != "loading") {
		fn();
	} else {
		document.addEventListener("DOMContentLoaded", fn);
	}
}

async function fetchData() {
	const response = await fetch(requester.ajax_url, {
		method: "post",
		headers: {
			"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
		},
		body: new URLSearchParams({
			action: "return_data",
			nonce: requester.nonce,
		}).toString(),
	});

	if (response.status === 200) {
		// Process data here
		let requesterEl = document.getElementById("requester");
		let title = document.createElement("h2");
		let data = await response.json();
		
		console.log(data);

		document.getElementById("loader").style.display = "none";
		
		if (data.error !== undefined) {
			title.innerText = data.error;
			requesterEl.append(title);
			return;
		} else {
			title.innerText = data.title;
			requesterEl.append(title);
			generateTable(requesterEl, data);
		}
			

	} else {
		// Rest of status codes (400,500,303), can be handled here appropriately
	}
}

function generateTable(element, data) {
	let tableData = data.data;

	console.log(tableData);

	let table = document.createElement("table");
	let tableHead = document.createElement("thead");
	let tableHeaderRow = document.createElement("tr");
	let tableBody = document.createElement("tbody");

	for (header in tableData.headers) {
		let tableTh = document.createElement("th");
		tableTh.innerText = tableData.headers[header];
		tableHeaderRow.append(tableTh);
	}

	tableHead.append(tableHeaderRow);
	table.append(tableHead);

	for (const row in tableData.rows) {
		const tableRow = document.createElement("tr");
		const rowData = tableData.rows[row];

		for (const cellData in rowData) {
			const tableTd = document.createElement("td");
			if (cellData == "date") {
				const date = new Date(rowData[cellData]);

				console.log(String(requester.locale));
				tableTd.innerText = date.toLocaleDateString(requester.locale);
				// tableTd.innerText = date.toLocaleDateString();
			} else {
				tableTd.innerText = rowData[cellData];
			}
			tableRow.append(tableTd);
		}

		tableBody.append(tableRow);
	}

	table.append(tableBody);

	element.append(table);
}

ready(fetchData());
