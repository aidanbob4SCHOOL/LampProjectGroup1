let userId = -1;
const urlBase = "http://localhost:8000/LAMPAPI"

function readCookie() {
    let data = document.cookie;
    let splits = data.split(",");

    for (var i = 0; i < splits.length; i++) {

        let thisOne = splits[i].trim();
        let tokens = thisOne.split("=");

        if (tokens[0] === "firstName") {
            firstName = tokens[1];
        }

        else if (tokens[0] === "lastName") {
            lastName = tokens[1];
        }

        else if (tokens[0] === "userId") {
            userId = parseInt(tokens[1].trim());
        }
    }

    if (userId < 0) {
        window.location.href = "index.html";
    }
}

function searchContacts(searchQuery) {
    readCookie();

    const contactList = document.getElementById("contactList");
    if (contactList == null) return;
    contactList.innerHTML = "";

    let tmp = {
        search: searchQuery,
        userId: userId
    };

    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + '/Search.php';

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                const response = JSON.parse(xhr.responseText);
                for (let contact of response.results) {
                    if (contact.phone === "") {
                        contact.phone = "(None)"
                    }
                    if (contact.email === "") {
                        contact.email = "(None)"
                    }
                    contactList.innerHTML += `
                        <div class="contact-card">
                        <div class="card-top"></div>
                        <div class="card-body">
                          <h3>${contact.firstName} ${contact.lastName}</h3>
                          <p class="contact-info">${contact.email} / ${contact.phone}</p>
                          <p class="note">${contact.notes}</p>
                          <div class="card-actions">
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                          </div>
                        </div>
                        </div>`
                }
            }
        };

        xhr.send(jsonPayload);
    } catch (err) {
        popError(err.message);
        console.log("Something went wrong with JSON: " + err.message);
    }
}

function listenSearch() {
    const searchBar = document.getElementById("searchText");
    if (searchBar == null) return;
    searchBar.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            searchContacts(searchBar.value);
        }
    })

    const searchButton = document.getElementById("searchBtn");
    if (searchButton == null) return;
    searchButton.addEventListener('click', (event) => {
        searchContacts(searchBar.value);
    })

    const resetButton = document.getElementById("resetBtn");
    if (resetButton == null) return;
    resetButton.addEventListener('click', (event) => {
        searchBar.value = "";
        searchContacts("");
    })
}

searchContacts("");
listenSearch();