requester = {
	...requester,
	element: document.getElementById("requester-content"),
	loader: {
		element: document.getElementById("loader"),
		show: function () {
			this.element.style.display = "block";
		},
		hide: function () {
			this.element.style.display = "none";
		},
	},
	init: async function (refresh = false) {
		const response = await this.fetchData(refresh);

		if (response.status === 200) {
			// Process data here
			let data = await response.json();

			requester.loader.hide();

			if (data.error !== undefined) {
				const errorLabel = document.createElement("label");
				errorLabel.innerText = data.error;
				this.element.append(errorLabel);
				return;
			} else {
				this.generateTable(this.element, data);
			}
		} else {
			throw new Error(`HTTP error! status: ${response.status}`);
			// Rest of status codes (400,500,303), can be handled here appropriately
		}
	},
	fetchData: async function (refresh = false) {
		requester.loader.show();

		const postData = {
			action: "return_data",
			nonce: requester.nonce,
		};

		if (refresh && requester.is_admin) {
			postData.refresh = true;
		}

		const response = await fetch(requester.ajax_url, {
			method: "post",
			headers: {
				"Content-type":
					"application/x-www-form-urlencoded; charset=UTF-8",
			},
			body: new URLSearchParams(postData).toString(),
		});

		return response;
	},
	generateTable: function (element, data) {
		const table = document.createElement("table"),
			title = document.createElement("caption"),
			tableHead = document.createElement("thead"),
			tableHeaderRow = document.createElement("tr"),
			tableBody = document.createElement("tbody");
			tableData = data.data;

		title.innerText = data.title;
		table.append(title);

		for (header in tableData.headers) {
			const tableTh = document.createElement("th");
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

					tableTd.innerText = date.toLocaleDateString(
						requester.locale
					);
				} else {
					tableTd.innerText = rowData[cellData];
				}
				tableRow.append(tableTd);
			}

			tableBody.append(tableRow);
		}

		table.append(tableBody);
		element.append(table);
	},
};

document.addEventListener("load", requester.init());

if (requester.is_admin) {
	document.getElementById("requester-refresh-button").addEventListener(
		"click",
		function (e) {
			const oldTable = requester.element.getElementsByTagName("table")[0];

			if (oldTable !== undefined) {
				oldTable.remove();
			}

			requester.init(true);
		},
		false
	);
}
