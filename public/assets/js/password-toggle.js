//Oculta y muestra la contraseña 
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.password-toggle-btn').forEach(function(button){

        button.addEventListener('click', function(){

            const input = document.getElementById(
                this.dataset.togglePassword
            );

            if(!input) return;

            const visible = input.type === 'password';

            input.type = visible ? 'text' : 'password';

            this.classList.toggle('is-visible', visible);
            this.setAttribute('aria-pressed', visible);

        });

    });

});
