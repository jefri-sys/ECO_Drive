<script>
    function validatePassword(formId) {
        const password = document.getElementById(`${formId}password`).value;
        const confirmPassword = document.getElementById(`${formId}cpassword`).value;
        const errorMessage = document.getElementById(`${formId}errorMessage`);

        // Reset error message
        if (errorMessage) {
            errorMessage.textContent = "";
        } else {
            console.error(`Error message element for ${formId} not found`);
            return false;
        }

        // Password complexity validation
        if (password.length < 8 || !/\d/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            errorMessage.textContent = "Password must be at least 8 characters, include a number, and a special character.";
            return false;
        }

        // Password match validation
        if (password !== confirmPassword) {
            errorMessage.textContent = "Passwords do not match!";
            return false;
        }

        // If everything is valid
        return true;
    }

    // Event listeners for Add Mechanic form
    document.getElementById("mechanicpassword").addEventListener("input", () => validatePassword("mechanic"));
    document.getElementById("mechaniccpassword").addEventListener("input", () => validatePassword("mechanic"));

    // Event listeners for Add Customer form
    document.getElementById("customerpassword").addEventListener("input", () => validatePassword("customer"));
    document.getElementById("customercpassword").addEventListener("input", () => validatePassword("customer"));

    // Submit listeners to prevent form submission if validation fails
    document.getElementById("addmechanic").addEventListener("submit", function (event) {
        if (!validatePassword("mechanic")) {
            event.preventDefault();
        }
    });

    document.getElementById("addcustomer").addEventListener("submit", function (event) {
        if (!validatePassword("customer")) {
            event.preventDefault();
        }
    });
</script>