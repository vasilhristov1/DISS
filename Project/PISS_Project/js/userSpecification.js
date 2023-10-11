//User icons
const anonymous = [
    "bird", 
    "crocodile", 
    "elephant",
    "giraffe",
    "kangaroo",
    "koala",
    "leopard",
    "nevmenqem",
    "octopus",
    "rhino",
    "snake",
    "squirrel",
    "tiger",
    "turtle",
    "zebra"    
];

var username;
var type;

//checks if user is logged or is using the table as guest
function isUserLoggedIn() {
    const url = "../php/currentUser.php";
    sendRequest(url, { method: 'GET' }, load, console.log);
}

//we get the info of the user (if logged)
function load(response) {
    if (response.success) {
        if (response["username"] && response["type"]) {
            username = response["username"];
            type = response["type"];
            loadLoggedUser();
        }
    } else {
    }

}

// if the user is a guest we add their functionality
// loads ther icon
function loadGuestUser(id) {
    var tableInfo = document.querySelector("#table-info");

    const randomImg = Math.floor(Math.random() * anonymous.length);

    var userImg = document.createElement("img");
    userImg.classList.add("user-img");
    userImg.setAttribute("src", `../images/${anonymous[randomImg]}.png`);
    userImg.setAttribute('id', id);
    userImg.style.position = "relative";
    userImg.title = `anonymous ${anonymous[randomImg]}`;
    
    tableInfo.appendChild(userImg);

    let icons = document.querySelectorAll('.user-img');

    iconsCount = icons.length;

    if (iconsCount > 1) {
        userImg.style.right = "0px";
    }

    for (let i = 0; i < iconsCount; i++) {
        icons[iconsCount - i - 1].style.right = `${-25*i}px`;
    }
}

// if the user is logged in we load their functionality
//removes login and register buttons
//adds logout button
// add a specific event listener for an input
// when the logged user types in a cell the cell is locket to all other users
// and receives an attribute "owner"

function loadLoggedUser() {
    let registerBtn = document.querySelector("#register-btn");
    let loginBtn = document.querySelector("#login-btn");
    let navBar = document.querySelector('.nav-list');

    navBar.removeChild(registerBtn.parentElement);
    navBar.removeChild(loginBtn.parentElement);

    let newLi = document.createElement('li');
    let icons = document.querySelector('.icons');

    let logout = document.createElement("button");
    logout.id = "logout-btn";
    logout.classList.add('table-button');
    logout.classList.add('logout-btn');
    logout.addEventListener("click", logoutUser);
    logout.innerText = "Logout";


    newLi.appendChild(logout);
    navBar.insertBefore(newLi, icons);

    let tableCells = document.querySelectorAll(".table-cell");
    tableCells.forEach(td => {
        if (td.getAttribute("owner") === username) {
            td.setAttribute("contenteditable", "");
        }
        td.addEventListener('input', () => {
            if (conn != null) {
                conn.send(`loggedUserChangeCell_${username}-` + td.id + "-" + td.innerText);
                td.setAttribute("owner", `${username}`);
            }
            if (td.innerText === "" && td.getAttribute) {
                td.removeAttribute("owner");
            }
        });
    });
}

//Logout function for when  the current $_SESSION is unset and destroyed (result:user logged out)
function logoutUser() {
    const url = "../php/logout.php";

    sendRequest(url, { method: 'POST' }, goToIndex, console.log);
}

function goToIndex(response) {
    if (response.success) {
        window.location = "index.html";
    }
}