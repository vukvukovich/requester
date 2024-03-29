requester = {
	...requester,
	element: document.getElementById("requester-data"),
	elements: {
		table: {
			loader: document.getElementById("table-loader"),
		},
	},
	init: async function (refresh = false) {
		const response = await this.fetchData(refresh);

		if (response.status === 200) {
			// Process data here
			let data = await response.json();

			this.hide(this.elements.table.loader);

			if (data.error !== undefined) {
				const errorLabel = document.createElement("label");
				errorLabel.innerText = data.error;
				this.element.append(errorLabel);
				return;
			} else {
				this.generateTable(this.element, data);

				if (refresh && this.is_admin) {
					this.show(this.elements.refreshButton.label);
					this.hide(this.elements.refreshButton.loader);
					this.enable(this.elements.refreshButton.element);
				}
			}
		} else {
			throw new Error(`HTTP error! status: ${response.status}`);
			// Rest of status codes (400,500,303), can be handled here appropriately
		}
	},
	fetchData: async function (refresh = false) {
		this.show(this.elements.table.loader);

		const postData = {
			action: "return_data",
			nonce: this.nonce,
		};

		if (refresh && this.is_admin) {
			postData.refresh = true;

			this.hide(this.elements.refreshButton.label);
			this.show(this.elements.refreshButton.loader);
			this.disable(this.elements.refreshButton.element);
		}

		const response = await fetch(this.ajax_url, {
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

		table.className = this.is_admin
			? "wp-list-table widefat fixed striped table-view-list"
			: "requester-table";
		title.innerText = data.title;
		table.append(title);

		for (header in tableData.headers) {
			const tableTh = document.createElement("th");
			tableTh.scope = "col";
			tableTh.id = tableData.headers[header]
				.replace(" ", "-")
				.toLowerCase();
			tableTh.className = "manage-column";
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

					tableTd.innerText = date.toLocaleDateString(this.locale);
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
	show: function (element) {
		element.style.display = "block";
	},
	hide: function (element) {
		element.style.display = "none";
	},
	enable: function (element) {
		element.disabled = false;
	},
	disable: function (element) {
		element.disabled = true;
	},
};

document.addEventListener("load", requester.init());

if (requester.is_admin) {
	requester.elements.refreshButton = {
		element: document.getElementById("requester-refresh-button"),
		label: document.getElementsByClassName("button-label")[0],
		loader: document.getElementsByClassName("button-loader")[0],
	};

	requester.elements.refreshButton.element.addEventListener(
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
