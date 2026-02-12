
//temporary before landing page
function goToLogin(){
    window.location.href = "login.html";
}

//Login Page Function (Switch between Login and Signup)
const tabs = document.querySelectorAll(".tab");
const forms = document.querySelectorAll(".form");

tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        const target = tab.dataset.target;

        // Tabs
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");

        // Forms
        forms.forEach(form => {
            form.classList.toggle("active", form.id === target);
        });
    });
});

//show/hide password
const togglePassword =
              document.querySelector('#togglePassword');

const password = document.querySelector('#password');

togglePassword.addEventListener('click', function (e) {
    // Toggle the type attribute 
    const type = password.getAttribute(
        'type') === 'password' ? 'text': 'password';
    password.setAttribute('type', type);

    // Toggle the eye slash icon 
    if (togglePassword.src.match("../images/hide_password.png")) {
        togglePassword.src ="../images/show_password.png";
    } else {
        togglePassword.src ="../images/hide_password.png";
    }
}); 

//username message stuff
let userform = document.getElementById("username");
let lettInput = document.getElementById("userLett");
let lenInput = document.getElementById("userLen");
let hypInput = document.getElementById("userHyp");
let undInput = document.getElementById("userUnd");

//username message displays
userform.onfocus = function () {
document.getElementById("explanationUser").style.display = "block";
};

//username message goes away
userform.onblur = function () {
document.getElementById("explanationUser").style.display = "none";
};

//username validation
userform.onkeyup = function () {
    var nums = /[0-9]/g;
    var lett = /[a-zA-Z]/g;
    var hyp = /[-]/g;
    var und = /[_]/g;

    //check length
    if ((userform.value.length >= 2 && userform.value.length <= 30)) {
      lenInput.classList.remove("invalid");
      lenInput.classList.add("valid");

    }

    else {
      lenInput.classList.remove("valid");
      lenInput.classList.add("invalid");

    }

    //check letters
    if (userform.value.match(lett) || userform.value.match(nums)) {

      lettInput.classList.remove("invalid");
      lettInput.classList.add("valid");
    }

    else {

      lettInput.classList.remove("valid");
      lettInput.classList.add("invalid");
    }

    //check hyphens
    if (userform.value.match(hyp)) {
      hypInput.classList.remove("opt");
      hypInput.classList.add("valid");
    }

    else {
      hypInput.classList.remove("valid");
      hypInput.classList.add("opt");
    }

    // check underscores
    if (userform.value.match(und)) {
      undInput.classList.remove("opt");
      undInput.classList.add("valid");
    }

    else {
      undInput.classList.remove("valid");
      undInput.classList.add("opt");
    }
};

//password message stuff
let passform = document.getElementById("password");
let pNumInput = document.getElementById("passNum");
let pLettInput = document.getElementById("passLett");
let pSpecInput = document.getElementById("passSpec");
let pLenInput = document.getElementById("passLen");

//password message displays
passform.addEventListener("focus", () => {
    document.getElementById("explanation").style.display = "block";
});

//password message goes away
passform.addEventListener("blur", () => {
    document.getElementById("explanation").style.display = "none";
});

//password validation
passform.onkeyup = function () {
    var nums = /[0-9]/g;
    var lett = /[a-zA-Z]/g;
    var spec = /[!@#$%^&*]/g;

    //check length
    if (passform.value.length >= 8 && passform.value.length <= 32) {
      pLenInput.classList.remove("invalid");
      pLenInput.classList.add("valid");
    }

    else {
      pLenInput.classList.remove("valid");
      pLenInput.classList.add("invalid");
    }

    //check numbers
    if (passform.value.match(nums)) {
      pNumInput.classList.remove("invalid");
      pNumInput.classList.add("valid");
    }

    else {
      pNumInput.classList.remove("valid");
      pNumInput.classList.add("invalid");
    }

    //check letters
    if (passform.value.match(lett)) {
      pLettInput.classList.remove("invalid");
      pLettInput.classList.add("valid");
    }

    else {
      pLettInput.classList.remove("valid");
      pLettInput.classList.add("invalid");
    }

    //check special characters
    if (passform.value.match(spec)) {
      pSpecInput.classList.remove("invalid");
      pSpecInput.classList.add("valid");
    }

    else {
      pSpecInput.classList.remove("valid");
      pSpecInput.classList.add("invalid");
    }
};

//Actual Functions

const urlBase = 'https://springucfpoosdap.com/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";
const ids = [];

function doLogin() {
    userId = 0;
    firstName = "";
    lastName = "";

    let login = document.getElementById("loginName").value;
    let password = document.getElementById("loginPassword").value;

    //var hash = md5(password);
    if (!validLoginForm(login, password)) {
        document.getElementById("result").style.display = "block";
        document.getElementById("loginResult").innerHTML = "invalid username or password";
        return;
    }
    document.getElementById("result").style.display = "none";
    document.getElementById("loginResult").innerHTML = "";
    let tmp = {
        login: login,
        password: password //hash
    };

    let jsonPayload = JSON.stringify(tmp);

    //Login.php API
    let url = urlBase + '/Login.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try {
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {

                let jsonObject = JSON.parse(xhr.responseText);
                userId = jsonObject.id;

                if (userId < 1) {
                    document.getElementById("result").style.display = "block";
                    document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
                    return;
                }
                firstName = jsonObject.firstName;
                lastName = jsonObject.lastName;

                saveCookie();
                window.location.href = "contacts.html";
            }
        };

        xhr.send(jsonPayload);
    } catch (err) {
        document.getElementById("result").style.display = "block";
        document.getElementById("loginResult").innerHTML = err.message;
    }
}

function doSignup() {
    firstName = document.getElementById("firstName").value;
    lastName = document.getElementById("lastName").value;

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    if (!validSignUpForm(firstName, lastName, username, password)) {
        document.getElementById("result").style.display = "block";
        document.getElementById("signupResult").innerHTML = "Invalid Signup";
        return;
    }

    //var hash = md5(password);
    document.getElementById("result").style.display = "none";
    document.getElementById("signupResult").innerHTML = "";

    let tmp = {
        firstName: firstName,
        lastName: lastName,
        login: username,
        password: password //hash
    };

    let jsonPayload = JSON.stringify(tmp);

    //SignUp.php API
    let url = urlBase + '/SignUp.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {

            if (this.readyState != 4) {
                return;
            }

            if (this.status == 409) {
                document.getElementById("result").style.display = "block";
                document.getElementById("signupResult").innerHTML = "User already exists";
                return;
            }

            if (this.status == 200) {

                let jsonObject = JSON.parse(xhr.responseText);
                userId = jsonObject.id;
                document.getElementById("result").style.display = "block";
                document.getElementById("signupResult").innerHTML = "User added";
                firstName = jsonObject.firstName;
                lastName = jsonObject.lastName;
                saveCookie();
            }
        };

        xhr.send(jsonPayload);
    } catch (err) {
        document.getElementById("result").style.display = "block";
        document.getElementById("signupResult").innerHTML = err.message;
    }
}

function validLoginForm(logName, logPass) {

    var logNameErr, logPassErr = true;

    if (logName == "") {
        console.log("USERNAME EMPTY");
    }
    else {
        let regex = /(?=.*[a-zA-Z])[a-zA-Z0-9-_]{2,30}$/;

        if (regex.test(logName) == false) {
            console.log("USERNAME INVALID");
        }

        else {

            console.log("USERNAME VALID");
            logNameErr = false;
        }
    }

    if (logPass == "") {
        console.log("PASSWORD EMPTY");
        logPassErr = true;
    }
    else {
        let regex = /(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*]).{8,32}/;

        if (regex.test(logPass) == false) {
            console.log("PASSWORD INVALID");
        }

        else {

            console.log("PASSWORD VALID");
            logPassErr = false;
        }
    }

    if ((logNameErr || logPassErr) == true) {
        return false;
    }
    return true;

}

function validSignUpForm(fName, lName, user, pass) {

    var fNameErr, lNameErr, userErr, passErr = true;

    if (fName == "") {
        console.log("FIRST NAME EMPTY");
    }
    else {
        console.log("FIRST NAME VALID");
        fNameErr = false;
    }

    if (lName == "") {
        console.log("LAST NAME EMPTY");
    }
    else {
        console.log("LAST NAME VALID");
        lNameErr = false;
    }

    if (user == "") {
        console.log("USERNAME EMPTY");
    }
    else {
        let regex = /(?=.*[a-zA-Z])([a-zA-Z0-9-_]).{2,30}$/;

        if (regex.test(user) == false) {
            console.log("USERNAME INVALID");
        }

        else {

            console.log("USERNAME VALID");
            userErr = false;
        }
    }

    if (pass == "") {
        console.log("PASSWORD EMPTY");
    }
    else {
        let regex = /(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*]).{8,32}/;

        if (regex.test(pass) == false) {
            console.log("PASSWORD INVALID");
        }

        else {

            console.log("PASSWORD VALID");
            passErr = false;
        }
    }

    if ((fNameErr || lNameErr || userErr || passErr) == true) {
        return false;

    }

    return true;
}

