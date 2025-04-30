<?php
    session_start();

    if(!isset($_SESSION['id']) && !isset($_SESSION['email'])){
        //header("Location: login.php");
        //exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main page</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body onload="addRow()">
    <div id="navigation">
        <a href="#home" class="nav-link">Home</a>
        <a href="#manage" class="nav-link">Manage</a>
        <a href="#storage" class="nav-link">Storage</a>
        <a href="#info" class="nav-link">Info</a>
    </div>

    <div id="home" class="body-container active">
        <button type="button" onclick="window.location.href='#createNew';">Create New</button>
    </div>

    <div id="manage" class="body-container">
        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "predracuni_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                header("Location: login.php?ex=2");
                exit();
            }

            $query = "SELECT p.id AS prebill_id, p.user_info, p.active, p.date, p.discount, 
                             c.id AS company_id, c.name AS company_name, c.phone AS company_phone
                      FROM Prebills p
                      JOIN Company c ON p.company_id = c.id
                      ORDER BY p.date DESC
                      LIMIT 15";

            $result = $conn->query($query);

            if ($result) {
                $bills = $result->fetch_all(MYSQLI_ASSOC);
                    foreach ($bills as $bill) {
                        $date = new DateTime($bill['date']);
                        $formattedDate = $date->format('F j, Y');
                        echo "
                                <a href='#createNew?id=" . $bill['prebill_id'] . "'>
                                    <div class='item-container'>
                                        <b> Bill num. " . $bill['prebill_id'] . "</b>
                                        <p> Created: " . $formattedDate . "</p>
                                        <p> For: " . $bill['user_info'] . "</p>
                                        <p> Status: " . ($bill['active'] ? "<span style='color: green;'>Active</span>" : "<span style='color: red;'>Inactive</span>") . " </p>
                                        
                                    </div>
                                </a>
                             ";
                    } 
            } 
        ?>
    </div>

    <div id="storage" class="body-container">
        <table id="storageTable" border="1">
            <tr>
              <th>Name</th>
              <th>Quantity</th>
              <th>Increase</th>
              <th>Action</th>
            </tr>
        </table>
    </div>

    <div id="info" class="body-container">
        <p>[User@archlinux ~]$ There is no info</p>
    </div>

    <div id="createNew" class="body-container">
        <h2 id="title2">Create new bill</h2>
        <input type="text" id="billingInfo" placeholder="Billing info" require>
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
    </div>
</body>
</html>

<script> // create table/parse
    let debounceTimer;

    function addRow() {
      const table = document.getElementById("dataTable");
      const row = table.insertRow();
      const cell1 = row.insertCell(0);
      const cell2 = row.insertCell(1);
      const cell3 = row.insertCell(2);
      const cell4 = row.insertCell(3);
      const cell5 = row.insertCell(4);

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
        checkAndRemoveEmptyRows();
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
                   oninput="checkStock(${availableStock}, '${id}')">
            <div id="stock_message_${id}" style="color: red;"></div>`;

          if (row.rowIndex === table.rows.length - 1) {
            addRow();
          }
        });
    }

    function checkStock(available, id) {
      const input = document.getElementById(`quantity_${id}`);
      const msgDiv = document.getElementById(`stock_message_${id}`);
      const quantity = parseInt(input.value, 10);

      if (isNaN(quantity) || quantity < 1) {
        msgDiv.textContent = "Please enter a valid quantity.";
        input.value = 1;
        return;
      }

      if (quantity > available) {
        msgDiv.textContent = `Only ${available} in stock.`;
        input.value = available;
      } else {
        msgDiv.textContent = "";
      }
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
            emptyCount--;
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

      const billingInfo = document.getElementById("billingInfo").value.trim();

      if (!billingInfo) {
        alert("Please enter billing information.");
        return;
      }

      if (ids.length > 0) {
        const queryString = `ids=${encodeURIComponent(ids.join(','))}&quantities=${encodeURIComponent(quantities.join(','))}&info=${encodeURIComponent(billingInfo)}`;
        window.location.href = `checkout.php?${queryString}`;
      } else {
        alert("Please enter ID and quantity for at least one item.");
      }
    }

    function fetchPrebill(prebillId) {
       fetch(`get_prebill.php?id=${prebillId}`)
        .then(res => res.json())
        .then(products => {
          clearTable();
          const table = document.getElementById("dataTable");

          products.forEach(product => {
            const row = table.insertRow();
            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            const cell3 = row.insertCell(2);
            const cell4 = row.insertCell(3);
            const cell5 = row.insertCell(4);

            const input = document.createElement("input");
            input.type = "text";
            input.className = "styled-text";
            input.value = product.id;
            input.oninput = function () {
              clearTimeout(debounceTimer);
              debounceTimer = setTimeout(() => searchID(input), 500);
            };

            cell1.appendChild(input);
            cell2.innerHTML = `<div class="result">${product.name}</div>`;
            cell3.innerHTML = `${product.description}`;
            cell4.innerHTML = `${product.price}$`;
            cell5.innerHTML = `
              <input type="number" min="1" value="${product.amount}" id="quantity_${product.id}" 
                     oninput="checkStock(9999, '${product.id}')">
              <div id="stock_message_${product.id}" style="color: red;"></div>`;
          });

          addRow();
          showSection('#createNew');
        });

    }


    function clearTable() {
        const table = document.getElementById("dataTable");
        while (table.rows.length > 1) {
            table.deleteRow(1);
        }
    }


    window.addEventListener('popstate', () => {
      const hash = window.location.hash;
      if (hash.startsWith('#createNew?id=')) {
        const id = new URLSearchParams(hash.split('?')[1]).get('id');
        if (id) {
          fetchPrebill(id);
        }
      }
    });

</script>

<script> // nav/hash control
    function cleanHash(hash) {
        return hash.split('?')[0].split('&')[0];
    }

    function showSection(hash) {
        if (hash.startsWith('#createNew?id=')) {
            const id = new URLSearchParams(hash.split('?')[1]).get('id');
            if (id) 
                fetchPrebill(id);
        }

        const cleanedHash = cleanHash(hash);

        document.querySelectorAll('.body-container').forEach(div => {
            div.classList.remove('active');
        });

        if (cleanedHash) {
            let activeSection = document.querySelector(cleanedHash);
            if (activeSection) {
                activeSection.classList.add('active');
            }
        }

        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('selected');
        });

        let activeLink = document.querySelector(`a[href="${cleanedHash}"]`);
        if (activeLink) {
            activeLink.classList.add('selected');
        }
    }


    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            let hash = this.getAttribute('href');
            history.pushState(null, null, hash);
            showSection(hash);
        });
    });

    window.addEventListener('load', () => {
        showSection(window.location.hash || '#home');
    });

    window.addEventListener('popstate', () => {
        showSection(window.location.hash);
    });
</script>

<script> // storage control
    function loadStorage() {
      fetch('get_storage.php')
        .then(res => res.json())
        .then(products => {
          const table = document.getElementById('storageTable');
          products.forEach(product => {
            const row = document.createElement('tr');

            row.innerHTML = `
              <td>${product.name}</td>
              <td id="q-${product.id}">${product.amount}</td>
              <td><input type="number" id="add-${product.id}" value="0" min="1" style="width:60px;"></td>
              <td><button onclick="increaseQuantity(${product.id})">+</button></td>
            `;

            table.appendChild(row);
          });
        })
        .catch(err => console.error("Error loading storage:", err));
    }

    function increaseQuantity(id) {
      const amount = parseInt(document.getElementById(`add-${id}`).value);
      if (isNaN(amount) || amount <= 0) return alert("Enter valid quantity");

      fetch('update_storage.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}&amount=${encodeURIComponent(amount)}`
      })
      .then(res => res.text())
      .then(response => {
        console.log(response);
        const current = document.getElementById(`q-${id}`);
        current.textContent = parseInt(current.textContent) + amount;
      })
      .catch(err => console.error("Update error:", err));
    }
    loadStorage();
</script>