let token = "";
const urlBase = "https://springucfpoosdap.com/LAMPAPI"

function readCookie() {
    let data = document.cookie;
    let splits = data.split(",");

    for (let i = 0; i < splits.length; i++) {

        let thisOne = splits[i].trim();
        let tokens = thisOne.split("=");

        if (tokens[0] === "firstName") {
            firstName = tokens[1];
        }

        else if (tokens[0] === "lastName") {
            lastName = tokens[1];
        }

        else if (tokens[0] === "token") {
            token = tokens[1];
        }
    }

    if (token === "") {
        window.location.href = "login.html";
    }
}

function searchContacts(searchQuery) {
    readCookie();

    const contactList = document.getElementById("contactList");
    if (contactList == null) return;
    contactList.innerHTML = "";

    let tmp = {
        search: searchQuery,
        token: token
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

                if (response.error !== "") {
                    console.error(response.error);
                    window.location.href = "login.html";
                }

                for (let contact of response.results) {
                    if (contact.lastName === "") {
                        contact.lastName = "";
                    }
                    if (contact.phone === "") {
                        contact.phone = "(None)"
                    }
                    if (contact.email === "") {
                        contact.email = "(None)"
                    }
                    if (contact.notes === "") {
                        contact.notes = "";
                    }
                    contactList.innerHTML += `
                        <div class="contact-card" data-first="${contact.firstName}" data-last="${contact.lastName}" data-email="${contact.email}" data-phone="${contact.phone}" data-notes="${contact.notes}" data-id="${contact.id}">
                        <div class="card-top"></div>
                        <div class="card-body">
                          <h3>${contact.firstName} ${contact.lastName}</h3>
                          <p class="contact-info">${contact.email} / ${contact.phone}</p>
                          <p class="notes">${contact.notes}</p>
                          <div class="card-actions">
                            <button class="edit-btn"">Edit</button>
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

function addContact() {
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    const notes = document.getElementById("notes").value;

    const tmp = {
        firstName: firstName,
        lastName: lastName,
        email: email,
        phoneNumber: phone,
        notes: notes,
        token: token
    };

    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + '/AddContact.php';

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.error !== "") {
                    console.error(response.error);
                    window.location.href = "login.html";
                }
                document.getElementById("popup").style.display = "none";
                firstName.value = "";
                lastName.value = "";
                email.value = "";
                phone.value = "";
                notes.value = "";
                searchContacts("");
            }
        };

        xhr.send(jsonPayload);
    } catch (err) {
        popError(err.message);
        console.log("Something went wrong with JSON: " + err.message);
    }
}

function editContact() {
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    const notes = document.getElementById("notes").value;
    const id = document.getElementById("id").value;

    const tmp = {
        firstName: firstName,
        lastName: lastName,
        email: email,
        phone: phone,
        notes: notes,
        contactId: id,
        token: token
    };

    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + '/EditContact.php';

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.error !== "") {
                    console.error(response.error);
                    window.location.href = "login.html";
                }
                document.getElementById("popup").style.display = "none";
                id.value = "";
                firstName.value = "";
                lastName.value = "";
                email.value = "";
                phone.value = "";
                notes.value = "";
                searchContacts("");
            }
        };

        xhr.send(jsonPayload);
    } catch (err) {
        popError(err.message);
        console.log("Something went wrong with JSON: " + err.message);
    }
}

function deleteContact() {
    const id = document.getElementById("id").value;

    const tmp = {
        contactId: id,
        token: token
    };

    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + '/DeleteContact.php';

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.error !== "") {
                    console.error(response.error)
                }
                document.getElementById("popup").style.display = "none";
                searchContacts("");
            }
        };

        xhr.send(jsonPayload);
    } catch (err) {
        popError(err.message);
        console.log("Something went wrong with JSON: " + err.message);
    }
}

function listenPopup() {
    const popup = document.getElementById("popup");
    const popupContent = document.getElementById("popup-content");
    const addContactButton = document.getElementsByClassName("add-btn")[0]

    addContactButton.addEventListener('click', () => {
        popupContent.innerHTML = `
            <span id="close">&times;</span>
            <div class="card-top"></div>
            <h3 class="card-body">Add Contact</h3>
            <form id="contactForm" class="card-body">
                <input type="text" id="firstName" placeholder="First Name" class="contactFormInput" required="required"><br>
                <input type="text" id="lastName" placeholder="Last Name" class="contactFormInput"><br>
                <input type="text" id="phone" placeholder="Phone Number" class="contactFormInput"><br>
                <input type="text" id="email" placeholder="Email" class="contactFormInput"><br>
                <textarea id="notes" placeholder="Notes" class="contactFormInput"></textarea><br>
                <input type="button" value="Add" id="contactFormSubmitButton" class="contactFormInput">
            </form>
        `
        listenFormEnter();
        popup.style.display = "block";

        const closeButton = document.getElementById("close");
        closeButton.addEventListener('click', () => {
            popup.style.display = "none";
        });

        const submitButton = document.getElementById("contactFormSubmitButton")
        submitButton.addEventListener('click', () => {
            addContact();
        })
    });

    const closeButton = document.getElementById("close");
    closeButton.addEventListener('click', () => {
        popup.style.display = "none";
    });

    const cardGrid = document.getElementById("contactList");
    cardGrid.addEventListener('click', (event) => {
        const editButton = event.target.closest('.edit-btn');
        if (!editButton) return;

        const card = editButton.closest('.contact-card');
        if (!card) return;

        const contact = {
            firstName: card.dataset.first,
            lastName: card.dataset.last !== "(None)" ? card.dataset.last : "",
            phone: card.dataset.phone !== "(None)" ? card.dataset.phone : "",
            email: card.dataset.email !== "(None)" ? card.dataset.email : "",
            notes: card.dataset.notes !== "(None)" ? card.dataset.notes : "",
            id: card.dataset.id
        };
        const popupContent = document.getElementById("popup-content");
        const popup = document.getElementById("popup");

        popupContent.innerHTML = `
            <span id="close">&times;</span>
            <div class="card-top"></div>
            <h3 class="card-body">Edit Contact</h3>
            <form id="contactForm" class="card-body">
                <input type="text" id="firstName" placeholder="First Name" class="contactFormInput" required="required"><br>
                <input type="text" id="lastName" placeholder="Last Name" class="contactFormInput"><br>
                <input type="text" id="phone" placeholder="Phone Number" class="contactFormInput"><br>
                <input type="text" id="email" placeholder="Email" class="contactFormInput"><br>
                <textarea id="notes" placeholder="Notes" class="contactFormInput"></textarea><br>
                <input type="hidden" id="id">
                <input type="button" value="Save" id="contactFormSubmitButton" class="contactFormInput">
            </form>
        `
        listenFormEnter();

        const closeButton = document.getElementById("close");
        closeButton.addEventListener('click', () => {
            popup.style.display = "none";
        });

        document.getElementById("firstName").value = contact.firstName;
        document.getElementById("lastName").value = contact.lastName;
        document.getElementById("email").value = contact.email;
        document.getElementById("phone").value = contact.phone;
        document.getElementById("notes").value = contact.notes;
        document.getElementById("id").value = contact.id;

        popup.style.display = "block";

        const submitButton = document.getElementById("contactFormSubmitButton")
        submitButton.addEventListener('click', () => {
            editContact();
        })
    })

    cardGrid.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('.delete-btn');
        if (!deleteButton) return;

        const card = deleteButton.closest('.contact-card');
        if (!card) return;

        const contact = {
            firstName: card.dataset.first,
            lastName: card.dataset.last !== "(None)" ? card.dataset.last : "",
            phone: card.dataset.phone !== "(None)" ? card.dataset.phone : "",
            email: card.dataset.email !== "(None)" ? card.dataset.email : "",
            notes: card.dataset.notes !== "(None)" ? card.dataset.notes : "",
            id: card.dataset.id
        };
        const popupContent = document.getElementById("popup-content");
        const popup = document.getElementById("popup");

        popupContent.innerHTML = `
            <span id="close">&times;</span>
            <div class="card-top"></div>
            <h3 class="card-body">Delete Contact</h3>
            <form id="contactForm" class="card-body">
                <input type="text" id="firstName" placeholder="First Name" class="contactFormInput" disabled="disabled"><br>
                <input type="text" id="lastName" placeholder="Last Name" class="contactFormInput" disabled="disabled"><br>
                <input type="text" id="phone" placeholder="Phone Number" class="contactFormInput" disabled="disabled"><br>
                <input type="text" id="email" placeholder="Email" class="contactFormInput" disabled="disabled"><br>
                <textarea id="notes" placeholder="Notes" class="contactFormInput" disabled="disabled"></textarea><br>
                <input type="hidden" id="id">
                <input type="button" value="Delete" id="contactFormSubmitButton" class="contactFormInput invalid">
            </form>
        `
        listenFormEnter();

        const closeButton = document.getElementById("close");
        closeButton.addEventListener('click', () => {
            popup.style.display = "none";
        });

        document.getElementById("firstName").value = contact.firstName;
        document.getElementById("lastName").value = contact.lastName;
        document.getElementById("email").value = contact.email;
        document.getElementById("phone").value = contact.phone;
        document.getElementById("notes").value = contact.notes;
        document.getElementById("id").value = contact.id;

        popup.style.display = "block";

        const submitButton = document.getElementById("contactFormSubmitButton")
        submitButton.addEventListener('click', () => {
            deleteContact();
        })
    })

    window.onclick = function(event) {
        if (event.target === popup) {
            popup.style.display = "none";
        }
    }
}

function listenLogout() {
    const logoutLink = document.getElementById("logout");
    logoutLink.addEventListener('click', () => {
        document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
    })
}

function listenFormEnter() {
    const inputFields = ["firstName", "lastName", "phone", "email", "notes"];

    for (const id of inputFields) {
        const field = document.getElementById(id);
        field.addEventListener('keypress', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                document.getElementById("contactFormSubmitButton").click();
            }
        })
    }
}

searchContacts("");
listenSearch();
listenPopup();
listenLogout();