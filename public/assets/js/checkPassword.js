/**
 * To check each password constraint and validate it as soon as it's ok,
 * One check icon on each line.
 */

document.querySelector(".password_to_check").addEventListener("input", checkPassword);

function checkPassword() {
    let password = this.value;
    //Here are the spans we want to add the check icon in
    let length = document.querySelector("#length");
    let upper = document.querySelector("#upper");
    let lower = document.querySelector("#lower");
    let digit = document.querySelector("#digit");
    let special = document.querySelector("#special");
    let space = document.querySelector("#space");

    if (password.length >= 8 && password.length <= 16) {
        length.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        length.innerHTML = '';
    }
    if (/[A-Z]/.test(password)) {
        upper.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        upper.innerHTML = '';
    }
    if (/[a-z]/.test(password)) {
        lower.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        lower.innerHTML = '';
    }
    if (/[0-9]/.test(password)) {
        digit.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        digit.innerHTML = '';
    }
    if (/\W/.test(password)) {
        special.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        special.innerHTML = '';
    }
    if (!/\s/.test(password) && password.length != 0) {
        space.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        space.innerHTML = '';
    }
}
