const inputPasswords = document.querySelectorAll('input[type="password"]');

if (inputPasswords) {
    inputPasswords.forEach(input => {
        const divParent = document.createElement('div');
        divParent.classList.add('parent-input-password');

        const icon = document.createElement('i')
        icon.classList.add('bi', 'bi-eye-slash-fill', 'icon-password');

        input.before(divParent);
        divParent.append(input, icon); // ajoute dans divParent à la fin

        icon.addEventListener('click', e => {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.type = type;

            if (type === 'password') {
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        });
    });
}