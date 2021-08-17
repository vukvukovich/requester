function ready(fn) {
	if (document.readyState != "loading") {
		fn();
	} else {
		document.addEventListener("DOMContentLoaded", fn);
	}
}

async function fetchAndDisplayData() {
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
		let requesterEl = document.getElementById("requester-table");
		let data = await response.json();

		console.log(data);

		document.getElementById("loader").remove();

		if (data.error !== undefined) {
			const errorLabel = document.createElement("label");
			errorLabel.innerText = data.error;
			requesterEl.append(errorLabel);
			return;
		} else {
			generateTable(requesterEl, data);
		}
	} else {
		throw new Error(`HTTP error! status: ${response.status}`);
		// Rest of status codes (400,500,303), can be handled here appropriately
	}
}

function generateTable(element, data) {
	let tableData = data.data;

	console.log(tableData);

	let table = document.createElement("table");
	let title = document.createElement("caption");

	title.innerText = data.title;
	table.append(title);

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

				tableTd.innerText = date.toLocaleDateString(requester.locale);
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

ready(fetchAndDisplayData());
