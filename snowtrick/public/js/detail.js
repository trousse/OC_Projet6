document.addEventListener('DOMContentLoaded', () => {

        // add event on trigger button
    function activeTrashButton() {
        const trash_btn = document.querySelectorAll(".trash_btn");
        trash_btn.forEach((trash) => {
            trash.addEventListener('click', (event) => {
                let myInit = {
                    method: trash.data.method,
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

    const send_video = document.querySelector("#send_video");
    send_video.addEventListener('click', (event) => {
        let data = new FormData();
        data.append('url',document.querySelector('#add_video').value);

        fetch(send_video.dataset.path, {
            method: 'POST',
            body: data
        }).then(response => response.json())
            .then(data => {
                document.querySelector("#add_video_container").insertAdjacentHTML('afterend', data.html);
                activeTrashButton();
            });
    });

    const add_image = document.querySelector("#add_image");
    add_image.addEventListener('change', (file) => {
        let data = new FormData();
        data.append('image', add_image.files[0]);

        fetch(add_image.dataset.path, {
            method: 'POST',
            body: data
        }).then(response => response.json())
            .then(data => {
                document.querySelector("#add_image_container").insertAdjacentHTML('afterend', data.html);
                activeTrashButton();
            });
    });

    const edit_main_image = document.querySelector('#edit_main_image');
    if(edit_main_image){
        edit_main_image.addEventListener('change', (file) => {
            let data = new FormData();
            data.append('image', edit_main_image.files[0]);
            fetch(edit_main_image.dataset.path, {
                method: 'POST',
                body: data
            }).then(response => response.json())
                .then(data => {
                    if(data.status === 'OK'){
                        document.querySelector("#main_img").src =  "/images/photos/trick_"+ data.id +"/"+data.name;
                        document.querySelector("#add_image_container").insertAdjacentHTML('afterend', data.html);
                        activeTrashButton();
                    }
                });
        });
    }
});