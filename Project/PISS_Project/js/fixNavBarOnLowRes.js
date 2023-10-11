let navbarItems = document.querySelectorAll('.nav-list li');
let navList = document.querySelector('.nav-list');

window.addEventListener('load', () => {
    if (window.screen.width > 900 && window.screen.width < 1100) {
        let divWrapper = document.createElement('div');
        divWrapper.classList.add('nav-buttons-wrapper');

        for (let i = 0; i < navbarItems.length - 1; i++) {
            divWrapper.appendChild(navbarItems[i]);
        }

        navList.appendChild(divWrapper);
    }
});