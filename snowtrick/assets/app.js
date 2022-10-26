/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application

document.addEventListener('DOMContentLoaded', () => {
// Functions to open and close a modal
    function openModal($el) {
        $el.classList.add('is-active');
    }

    function closeModal($el) {
        $el.classList.remove('is-active');
    }

    function closeAllModals() {
        (document.querySelectorAll('.modal') || []).forEach(($modal) => {
            closeModal($modal);
        });
    }

// Add a click event on buttons to open a specific modal
    (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
        const modal = $trigger.dataset.target;
        const $target = document.getElementById(modal);

        $trigger.addEventListener('click', () => {
            openModal($target);
        });
    });

// Add a click event on various child elements to close the parent modal
    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
        const $target = $close.closest('.modal');

        $close.addEventListener('click', () => {
            closeModal($target);
        });
    });
});

// add event on trigger button
function activeTrashButton() {
    const trash_btn = document.querySelectorAll(".trash_btn");
    trash_btn.forEach((trash) => {
        trash.addEventListener('click', (event) => {
            let myInit = {
                method: 'POST',
                headers: {'content-type': 'application/json'}
            };

            fetch(trash.dataset.path, myInit)
                .then((res) => {
                    document.querySelector("#" + trash.dataset.entity + "_" + trash.dataset.id).outerHTML = "";
                    closeAllModals();
                });
        })
    })
}

activeTrashButton();

let tricksPerPage = 6;
let tricks = document.querySelectorAll("#tricks > .column");
if (tricks.length !== 0) {
    if (tricks.length <= tricksPerPage) {
        document.querySelector("#more_trick_button").classList.add('is-hidden');
    }

    for (let i = tricksPerPage; i <= tricks.length - 1; i++) {
        tricks[i].classList.add('is-hidden');
    }

    document.querySelector("#more_trick_button > button").addEventListener('click', () => {
        tricksPerPage += 6;
        if (tricks.length <= tricksPerPage) {
            document.querySelector("#more_trick_button").classList.add('is-hidden');
        }
        for (var i = 0; i < tricksPerPage; i++) {
            if (!tricks[i]) {
                continue;
            }
            tricks[i].classList.remove('is-hidden');
        }
    })
}


let commentPerPage = 3;
let comment = document.querySelectorAll("#comments > .card");
if (comment.length !== 0) {
    if (comment.length <= commentPerPage) {
        document.querySelector("#comment_more_button").classList.add('is-hidden');
    }

    for (let i = commentPerPage; i <= comment.length - 1; i++) {
        comment[i].classList.add('is-hidden');
    }

    document.querySelector("#comment_more_button").addEventListener('click', () => {
        commentPerPage += 3;
        if (comment.length <= commentPerPage) {
            document.querySelector("#comment_more_button").classList.add('is-hidden');
        }
        for (var i = 0; i < commentPerPage; i++) {
            if (!comment[i]) {
                continue;
            }
            comment[i].classList.remove('is-hidden');
        }
    });
}

