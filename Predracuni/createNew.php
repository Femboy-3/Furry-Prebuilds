<!DOCTYPE html>
<html>
<head>
  <title>Create new bill</title>
  <link rel="icon" href="resources/icon.png" type="image/png">
    <link rel="stylesheet" href="style3.css">
    <script>
  let debounceTimer;

  function addRow() {
    const table = document.getElementById("dataTable");
    const row = table.insertRow();
    const cell1 = row.insertCell(0); // ID input
    const cell2 = row.insertCell(1); // Name
    const cell3 = row.insertCell(2); // Description
    const cell4 = row.insertCell(3); // Price
    const cell5 = row.insertCell(4); // Quantity

    const input = document.createElement("input");
    input.type = "text";
    input.className = "styled-text";
    input.placeholder = "Enter ID";
    input.oninput = function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => searchID(input), 500);
    };

    cell1.appendChild(input);
    cell2.innerHTML = '<div class="result"></div>';
    cell3.innerHTML = '';
    cell4.innerHTML = '';
    cell5.innerHTML = '';
  }

  function searchID(input) {
  const id = input.value.trim();
  const row = input.parentElement.parentElement;
  const table = document.getElementById("dataTable");
  const resultDiv = row.cells[1].querySelector('.result');
  const cell2 = row.cells[1];
  const cell3 = row.cells[2];
  const cell4 = row.cells[3];
  const cell5 = row.cells[4];

  if (id === "") {
    resultDiv.innerHTML = "";
    cell2.innerHTML = '<div class="result"></div>';
    cell3.innerHTML = '';
    cell4.innerHTML = '';
    cell5.innerHTML = '';

    checkAndRemoveEmptyRows(); // 👈 call the cleanup function
    return;
  }

  fetch("search.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.text())
  .then(data => {
    if (data === "No product found.") {
      resultDiv.innerHTML = "Product not found!";
      cell3.innerHTML = '';
      cell4.innerHTML = '';
      cell5.innerHTML = '';
      return;
    }

    const [name, description, price, availableStock] = data.split('||');

    cell2.innerHTML = `<div class="result">${name}</div>`;
    cell3.innerHTML = `${description}`;
    cell4.innerHTML = `${price}$`;
    cell5.innerHTML = `
      <input type="number" min="1" value="1" id="quantity_${id}" 
             oninput="checkStock(${availableStock}, ${id})">
      <div id="stock_message_${id}" style="color: red;"></div>`;

    if (row.rowIndex === table.rows.length - 1) {
      addRow();
    }
  });
}



function checkAndRemoveEmptyRows() {
  const table = document.getElementById("dataTable");
  let emptyCount = 0;

  for (let i = table.rows.length - 1; i >= 1; i--) {
    const row = table.rows[i];
    const idInput = row.cells[0].querySelector("input");

    if (idInput && idInput.value.trim() === "") {
      emptyCount++;
      if (emptyCount >= 2) {
        table.deleteRow(i);
        emptyCount--; // reset to 1 after removing the row
      }
    }
  }
}


function redirectToCheckout() {
  const table = document.getElementById("dataTable");
  let ids = [];
  let quantities = [];

  for (let i = 1; i < table.rows.length; i++) {
    const row = table.rows[i];
    const idInput = row.cells[0].querySelector("input");
    const quantityInput = row.cells[4].querySelector("input");

    if (idInput && quantityInput) {
      const id = idInput.value.trim();
      const quantity = quantityInput.value.trim();
      if (id && quantity) {
        ids.push(id);
        quantities.push(quantity);
      }
    }
  }

  if (ids.length > 0) {
    const queryString = `ids=${encodeURIComponent(ids.join(','))}&quantities=${encodeURIComponent(quantities.join(','))}`;
    window.location.href = `checkout.php?${queryString}`;
  } else {
    alert("Please enter ID and quantity for at least one item.");
  }
}
</script>

</head>
<body onload="addRow()">
  <h2>Create new bill</h2>
  <table id="dataTable" border="1">
    <tr>
      <th>Product ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Price</th>
      <th>Quantity to Order</th>
    </tr>
  </table>
  <button onclick="redirectToCheckout()">Confirm order</button>
</body>
</html>