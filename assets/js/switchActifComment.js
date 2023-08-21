import { sendRequest } from "./sendRequest";

const switchsTag = document.querySelectorAll('input[js-switch-comment]');

if (switchsTag) {
    switchsTag.forEach(input => {
        input.addEventListener('change', e => {
            const id = e.target.dataset.id;
            sendRequest(`/admin/commentaires/switch/${id}`, e.target);
        });
    });
}