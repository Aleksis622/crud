// Get buttons and sections
const createUserBtn = document.getElementById('createUserBtn');
const readUserBtn = document.getElementById('readUserBtn');
const createUserSection = document.getElementById('createUserSection');
const readUserSection = document.getElementById('readUserSection');


function validateForm() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value.trim();
    const dateBirth = document.getElementById('date_birth').value;

    const nameRegex = /^[A-Za-zĀ-ž]{2,}$/; // Only letters including Latvian characters
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email validation
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{15,}$/; // Password with special symbols, minimum 15 characters
    const phoneRegex = /^[0-9]{8}$/; // Exactly 8 digits

;const allowedEmailDomains = ['gmail.com', 'yahoo.com', 'outlook.com ', 'inbox.lv']; // Add domains as needed
const emailDomain = email.split('@')[1];

    const today = new Date().toISOString().split('T')[0]; // format: YYYY-MM-DD

    // Validate First Name (letters, at least 2 characters)
    if (!nameRegex.test(firstName)) {
        alert("First name must be at least 2 letters and contain only letters.");
        return false;
    }

    // Validate Last Name (letters, at least 2 characters)
    if (!nameRegex.test(lastName)) {
        alert("Last name must be at least 2 letters and contain only letters.");
        return false;
    }

    // Validate Email format
    if (!emailRegex.test(email)) {
        alert("Invalid email format.");
        return false;
    }

    console.log(password); // Check the password value/ Validate Password (min 15 characters, at least one letter, one number, one special character)
    if (!passwordRegex.test(password)) {
        alert("Password must be at least 15 characters, including uppercase and lowercase letters, numbers, and special symbols.");
        return false;
    }

    // Validate Date of Birth (must be before today)
    if (!dateBirth || dateBirth >= today) {
        alert("Date of birth must be before today.");
        return false;
    }

    // Validate Phone Number (exactly 8 digits)
    if (!phoneRegex.test(phone)) {
        alert("Phone number must be exactly 8 digits.");
        return false;
    }

    return true;
}
//validacija beidzas seit


// Toggle sections when menu items are clicked
createUserBtn.addEventListener('click', () => {
    createUserSection.style.display = 'block';
    readUserSection.style.display = 'none';
});

readUserBtn.addEventListener('click', () => {
    createUserSection.style.display = 'none';
    readUserSection.style.display = 'block';
});

// Handle form submission (basic)
document.getElementById('userForm').addEventListener('submit', (e) => {
    e.preventDefault();
});
//create
document.getElementById('userForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    
    if (!validateForm()) {
        return; // If validation fails, do not proceed
    }

    let formData = new FormData();
    formData.append("firstName", document.getElementById('firstName').value);
    formData.append("lastName", document.getElementById('lastName').value);
    formData.append("email", document.getElementById('email').value);
    formData.append("phone", document.getElementById('phone').value);
    formData.append("password", document.getElementById('password').value);
    formData.append("date_birth", document.getElementById('date_birth').value);

    try {
        // Make the fetch request to the PHP script
        let response = await fetch('create_user.php', {
            method: 'POST',
            body: formData
        });

        // Ensure the response is valid and we received JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        
        // Check if the response body is HTML instead of JSON
        if (text.trim().startsWith("<")) {
            throw new Error("Server returned HTML instead of JSON. Check PHP errors.");
        }

        // Parse the JSON response safely
        const data = JSON.parse(text);
        console.log("✅ Parsed JSON:", data);
        alert(data.message);

    } catch (error) {
        console.error("❌ Error:", error);
        alert("Error: " + error.message);
    }

    console.log("✅ Sending this data:", {
        firstName: document.getElementById('firstName').value,
        lastName: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        password: document.getElementById('password').value,
        date_birth: document.getElementById('date_birth').value
    });
});

   
   // let result = await response.json();
   // alert(result.message);

;
   
//update form
document.getElementById('readUserBtn').addEventListener('click', async function () {
    let response = await fetch("read_users.php");
    let users = await response.json();
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('updateBtn')) {
            console.log("✅ Update button clicked");
            const row = e.target.closest('tr');
            const id = row.children[0].textContent;
            const firstName = prompt("Enter new first name:", row.children[1].textContent);
            const lastName = prompt("Enter new last name:", row.children[2].textContent);
            const email = prompt("Enter new email:", row.children[3].textContent);
            const phone = prompt("Enter new phone number:", row.children[4].textContent);
            const password = prompt("Enter new password:", row.children[5].textContent);
            const date = prompt("Enter new date of birth (YYYY-MM-DD):", row.children[6].textContent);
    
            if (firstName && lastName && email && phone && password && date) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('firstName', firstName);
                formData.append('lastName', lastName);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('password', password);
                formData.append('date_birth', date);
                fetch("update.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    console.log("🟢 Response from server:", result);
                    alert(result.message);
                    if (result.status === "success") {
                        document.getElementById('readUserBtn').click(); // Refresh list
                    }
                })
                .catch(error => {
                    console.error("❌ Error while updating:", error);
                    alert("Something went wrong while updating.");
                });
                if (!validatePhone(phone)) {
                    alert("Invalid phone number");
                    return;
                }
                
            } else {
                alert("All fields are required.");
            }
            
        }
    });
    
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('deleteBtn')) {
            console.log("✅ Delete button clicked");
            const row = e.target.closest('tr');
            const userId = row.children[0].textContent; // Get the user ID
            
            // Confirm the delete action
            if (confirm(`Are you sure you want to delete user with ID ${userId}?`)) {
                // Send a DELETE request to the server
                fetch(`delete_user.php?id=${userId}`, {
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(result => {
                    console.log("🟢 Response from server:", result);
                    alert(result.message);
                    if (result.status === "success") {
                        document.getElementById('readUserBtn').click(); // Refresh list
                    }
                })
                .catch(error => {
                    console.error("❌ Error while deleting:", error);
                    alert("Something went wrong while deleting.");
                });
            }
        }
    });

    let readSection = document.getElementById('readUserSection');
    readSection.innerHTML = "<h2>Users List</h2>";

    if (users.length > 0) {
        let table = `
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>email</th>
                        <th>phone number</th>
                        <th>password</th>
                         <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        users.forEach(user => {
            table += `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.first_name}</td> 
                    <td>${user.last_name}</td>
                    <td>${user.email}</td>
                    <td>${user.phone_number}</td>
                    <td>${user.password}</td>
                    <td>${user.date_birth}</td>
                    <td>
                        <button class="updateBtn" data-id="${user.id}">Update</button>
                        <button class="deleteBtn" data-id="${user.id}">Delete</button>
                    </td>
                </tr>
            `;
        });
        
        ocument.getElementById("fileInput").addEventListener("change", function (e) {
            const file = e.target.files[0];
            const filePreview = document.getElementById("filePreview");
            filePreview.innerHTML = ""; // Reset preview
        
            // Show a preview based on file type
            if (file) {
                const fileReader = new FileReader();
        
                fileReader.onload = function (event) {
                    const fileType = file.type;
                    const fileUrl = event.target.result;
        
                    if (fileType.startsWith("image/")) {
                        // Preview image
                        const img = document.createElement("img");
                        img.src = fileUrl;
                        img.style.maxWidth = "200px";  // Adjust preview size as needed
                        filePreview.appendChild(img);
                    } else if (fileType === "application/pdf") {
                        // Preview PDF (display icon)
                        filePreview.innerHTML = "<p>PDF Preview is not available here, but the file can be downloaded.</p>";
                    } else if (fileType.startsWith("audio/") || fileType.startsWith("video/")) {
                        // Preview audio/video files
                        const media = document.createElement(fileType.startsWith("audio/") ? "audio" : "video");
                        media.controls = true;
                        media.src = fileUrl;
                        filePreview.appendChild(media);
                    } else {
                        // Handle unsupported file types
                        filePreview.innerHTML = "<p>No preview available for this file type.</p>";
                    }
                };
        
                fileReader.readAsDataURL(file);
            }
        });

        table += `</tbody></table>`;
        readSection.innerHTML += table;
    } else {
        readSection.innerHTML += "<p>Not a single user is in our DB</p>";
    }

    document.getElementById('createUserSection').style.display = 'none';
    readSection.style.display = 'block';
});


