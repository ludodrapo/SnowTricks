/**
 * To check if the second given password matches the regex AND is strictly equal to the first one.
 * Display an error message below the input if not, a confirm message if it's ok.
 */

document.querySelector("#update_password_form_password_second").addEventListener("input", checkEqualPasswords);

function checkEqualPasswords() {
    let password_2 = this.value;
    let password_1 = document.getElementById("update_password_form_password_first").value;
    let block = document.getElementById("new_password_2_help-block");

    if (password_2 === password_1 && password_2.length != 0) {
        block.classList.replace('text-danger', 'text-info');
        block.innerHTML = "Les mots de passe sont identiques <i class='fas fa-check'></i>";
    } else if (/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s)([\W\w]{8,16})$/.test(password_2)
        && password_2 !== password_1) {
        block.classList.replace('text-info', 'text-danger');
        block.innerHTML = "Attention, le deuxième mot de passe respecte bien les critères mais n'est pas identique au premier. <i class='fas fa-exclamation-circle'></i>";
    } else {
        block.innerHTML = "";
    }
}
