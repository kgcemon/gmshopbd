const loginModal = document.getElementById('loginModal');
const closeModal = document.getElementById('closeModal');
const loginTriggers = document.querySelectorAll('#loginBtn, .login-trigger');

loginTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        loginModal.classList.add('show');
    });
});

closeModal.addEventListener('click', function() {
    loginModal.classList.remove('show');
});

window.addEventListener('click', function(e) {
    if(e.target === loginModal){
        loginModal.classList.remove('show');
    }
});
