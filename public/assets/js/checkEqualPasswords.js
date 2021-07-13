/**
 * To check if the second given password matches the regex AND is strictly equal to the first one.
 * Display an error message below the input if not, a confirm message if it's ok.
 */

document.querySelector("#update_password_form_password_second").addEventListener("input", checkEqualPasswords);

function checkEqualPasswords() {
    let password_2 = this.value;
    let password_1 = document.getElementById("update_password_form_password_first").value;
    // let current_password = document.getElementById("current_password");
    // let button = document.getElementById("submit_password_change");

    if (password_2 === password_1 && password_2.length != 0) {
        document.getElementById("new_password_2_help-block").classList.replace('text-danger', 'text-info');
        document.getElementById("new_password_2_help-block").innerHTML =
            "Les mots de passe sont identiques <i class='fas fa-check'></i>";
    } else if (/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s)([\W\w]{8,16})$/.test(password_2)
        && password_2 !== password_1) {
        document.getElementById("new_password_2_help-block").classList.replace('text-info', 'text-danger');
        document.getElementById("new_password_2_help-block").innerHTML =
            "Attention, le deuxième mot de passe respecte bien les critères mais n'est pas identique au premier. <i class='fas fa-exclamation-circle'></i>";
    } else {
        document.getElementById("new_password_2_help-block").innerHTML = "";
    }
}
