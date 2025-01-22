// Function to clear the form fields
function clearForm() {
    const fieldsToClear = [
        "firstName",
        "lastName",
        "DOB",
        "phone-number",
        "email",
        "address",
    ];

    fieldsToClear.forEach((id) => {
        const element = document.getElementById(id);
        if (element) {
            element.value = ""; // Clear input value
            if (element.type === "radio") {
                element.checked = false; // Uncheck radio buttons
            }
        }
    });
}

function previousPage() {
    const currentPage = document.getElementById("page" + currentStep);
    const previousPage = document.getElementById("page" + (currentStep - 1));
  
    if (currentStep > 1) {
      currentPage.classList.add("hidden");
      previousPage.classList.remove("hidden");
  
      document
        .getElementById("step" + currentStep)
        .classList.remove("active-step");
      document
        .getElementById("step" + (currentStep - 1))
        .classList.add("active-step");
  
      currentStep--;
  
      // Update progress bar
      updateProgressBar(currentStep);
    }
  }

// Track the current page
let currentStep = 1;
const totalPages = 5; // Total number of pages in the form

function nextPage() {
    const currentPage = document.getElementById("page" + currentStep);
    const nextPage = document.getElementById("page" + (currentStep + 1));

    if (validatePage(currentStep)) {
        currentPage.classList.add("hidden");
        nextPage.classList.remove("hidden");

        // Update step indicators
        document
            .getElementById("step" + currentStep)
            .classList.remove("active-step");
        document
            .getElementById("step" + (currentStep + 1))
            .classList.add("active-step");

        currentStep++;

        // Update progress bar
        updateProgressBar(currentStep);
    } else {
        alert("Please fill in all required fields.");
    }
}

// Function to validate the form on the current page
function validatePage(step) {
    let valid = true;

    if (step === 1) {
        const fieldsToValidate = [
            "firstName",
            "lastName",
            "DOB",
            "phone-number",
            "email",
        ];

        fieldsToValidate.forEach((id) => {
            const element = document.getElementById(id);
            if (!element.value) {
                valid = false;
            }
        });
    }

    return valid;
}

// Function to update the progress bar based on current step
function updateProgressBar(currentStep) {
    for (let i = 1; i <= totalPages; i++) {
        const step = document.getElementById(`step${i}`);
        step.classList.remove("active-step", "inactive-step");

        if (i <= currentStep) {
            step.classList.add("active-step");
        } else {
            step.classList.add("inactive-step");
        }
    }
}

 // Show/hide accident details based on selection
 document.getElementById('previous-accidents').addEventListener('change', function () {
    document.getElementById('accident-details').style.display = this.value === 'yes' ? 'block' : 'none';
});

// Show/hide suspension reason based on selection
document.getElementById('number-suspensions').addEventListener('change', function () {
    document.getElementById('suspension-reason').style.display = this.value === 'yes' ? 'block' : 'none';
});

// Show/hide offense details based on selection
document.getElementById('traffic-offenses').addEventListener('change', function () {
    document.getElementById('offense-details').style.display = this.value === 'yes' ? 'block' : 'none';
});

function toggleTimeInputs(dayId, dayName) {
    const timeInputsContainer = document.getElementById('time-inputs-container');
    const checkbox = document.getElementById(dayId);
    const existingInput = document.getElementById(`time-inputs-${dayId}`);

    // Check if the checkbox is checked
    if (checkbox.checked) {
        // Create a new div for time inputs only if it doesn't already exist
        if (!existingInput) {
            const timeInputsDiv = document.createElement('div');
            timeInputsDiv.id = `time-inputs-${dayId}`;
            timeInputsDiv.classList.add('time-inputs');
            timeInputsDiv.innerHTML = `
                <label for="start-time-${dayId}">${dayName} Start Time:</label>
                <input type="time" id="start-time-${dayId}" name="start-time-${dayId}"><br><br>
                <label for="end-time-${dayId}">${dayName} End Time:</label>
                <input type="time" id="end-time-${dayId}" name="end-time-${dayId}"><br>
            `;
            timeInputsContainer.appendChild(timeInputsDiv);
        }
    } else {
        // Remove the corresponding time inputs if the checkbox is unchecked
        if (existingInput) {
            timeInputsContainer.removeChild(existingInput);
        }
    }
}
