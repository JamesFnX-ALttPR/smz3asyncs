var myInput = document.getElementById("password");
var letterLower = document.getElementById("lower");
var letterUpper = document.getElementById("upper");
var number = document.getElementById("number");
var specialCharacter = document.getElementById("special");
var passwordLength = document.getElementById("length");
var passwordLonger = document.getElementById("longer");
var submitButton = document.getElementById("submitButton");
var confirmPassword = document.getElementById("confirmPassword");
var email = document.getElementById("email");
var display_name = document.getElementById("display_name");
var emailRegex = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/g;
var fullValidation = /.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/;

myInput.onfocus = function() {
    document.getElementById("passwordRequirements").style.display = "table-row";
}

myInput.onblur = function() {
    document.getElementById("passwordRequirements").style.display = "none";
}

email.onkeyup = function () {
    if(email.value.match(emailRegex)) {
        document.getElementById("validEmail").style.display = "none";
    } else {
        document.getElementById("validEmail").style.display = "table-row";
    }

    if((email.value.match(emailRegex)) && (display_name.value != null) && (myInput.value == confirmPassword.value) && (myInput.value.match(fullValidation) || myInput.value.length >= 16)) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }

}

display_name.onkeyup = function () {
    if((email.value.match(emailRegex)) && (display_name.value != null) && (myInput.value == confirmPassword.value) && (myInput.value.match(fullValidation) || myInput.value.length >= 16)) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }

}

myInput.onkeyup = function() {
    var lowerCaseLetters = /[a-z]/g;
    if(myInput.value.match(lowerCaseLetters)) {
        letterLower.classList.remove("invalid");
        letterLower.classList.add("valid");
    } else {
        letterLower.classList.remove("valid");
        letterLower.classList.add("invalid");
    }

    var upperCaseLetters = /[A-Z]/g;
    if(myInput.value.match(upperCaseLetters)) {
        letterUpper.classList.remove("invalid");
        letterUpper.classList.add("valid");
    } else {
        letterUpper.classList.remove("valid");
        letterUpper.classList.add("invalid");
    }
    
    var numbers = /[0-9]/g;
    if(myInput.value.match(numbers)) {
        number.classList.remove("invalid");
        number.classList.add("valid");
    } else {
        number.classList.remove("valid");
        number.classList.add("invalid");
    }

    var specials = /\W/g;
    if(myInput.value.match(specials)) {
        specialCharacter.classList.remove("invalid");
        specialCharacter.classList.add("valid");
    } else {
        specialCharacter.classList.remove("valid");
        specialCharacter.classList.add("invalid");
    }

    if(myInput.value.length >= 8) {
        passwordLength.classList.remove("invalid");
        passwordLength.classList.add("valid");
    } else {
        passwordLength.classList.remove("valid");
        passwordLength.classList.add("invalid");
    }
    
    if(myInput.value.length >= 16) {
        passwordLonger.classList.remove("invalid");
        passwordLonger.classList.add("valid");
    } else {
        passwordLonger.classList.remove("valid");
        passwordLonger.classList.add("invalid");
    }
    
    if((email.value.match(emailRegex)) && (display_name.value != null) && (myInput.value == confirmPassword.value) && (myInput.value.match(fullValidation) || myInput.value.length >= 16)) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }

}

confirmPassword.onkeyup = function() {
    if(myInput.value == confirmPassword.value && (myInput.value.match(fullValidation) || myInput.value.length >= 16)) {
        document.getElementById("matchingPassword").style.display = "none";
    } else {
                document.getElementById("matchingPassword").style.display = "table-row";
    }

    if((email.value.match(emailRegex)) && (display_name.value != null) && (myInput.value == confirmPassword.value) && (myInput.value.match(fullValidation) || myInput.value.length >= 16)) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }

}