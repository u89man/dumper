let u89mInit = window.u89mInit || function (root) {
    root = document.getElementById(root + '');

    function click(selector, callback) {
        root.querySelectorAll(selector).forEach(function (el) {
            el.addEventListener('click', function (event) {
                event.preventDefault();
                callback(event.target);
            });
        });
    }

    click('.toggle', function (el) {
        el.parentElement.classList.toggle('show');
        el.innerText = el.innerText === '>>' ? '<<' : '>>';
    });

    click('.namespace[data-ns]', function (el) {
        let str = el.innerText;
        el.innerText = el.dataset.ns;
        el.dataset.ns = str;
    });
};

