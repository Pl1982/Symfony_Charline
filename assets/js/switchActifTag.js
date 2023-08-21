import { sendRequest } from "./sendRequest";

const switchsTag = document.querySelectorAll('input[js-switch-tag]');

if (switchsTag) {
    switchsTag.forEach(input => {
        input.addEventListener('change', e => {
            const id = e.target.dataset.id;
            sendRequest(`/admin/categories/switch/${id}`, e.target);
        });
    });
}