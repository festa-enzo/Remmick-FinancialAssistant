// Js/Settings.js

const API_BASE = "http://api.remmick.com/api";

// -------------------------
// Elementos do HTML
// -------------------------

const profileForm = document.getElementById("profile-form");
const nameInput = document.getElementById("profile-name-input");
const emailInput = document.getElementById("profile-email-input");
const editProfileButton = document.getElementById("edit-profile");
const cancelProfileButton = document.getElementById("cancel-profile");
const saveProfileButton = document.getElementById("save-profile");

const passwordForm = document.getElementById("password-form");
const currentPasswordInput = document.getElementById("current-password");
const newPasswordInput = document.getElementById("new-password");
const confirmPasswordInput = document.getElementById("confirm-password");

const logoutButton = document.getElementById("logout");
const deleteAccountButton = document.getElementById("delete-account");

const userNameElement = document.getElementById("user-name");
const profileNameElement = document.getElementById("profile-name");
const profileEmailElement = document.getElementById("profile-email");

// -------------------------
// Sessão e requisições
// -------------------------

function getToken() {
    return localStorage.getItem("token");
}

function getStoredUser() {
    try {
        return JSON.parse(localStorage.getItem("user")) || {};
    } catch {
        return {};
    }
}

function clearSession() {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
}

async function apiRequest(endpoint, options = {}) {
    const token = getToken();

    const headers = {
        ...(options.headers || {})
    };

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    if (options.body) {
        headers["Content-Type"] = "application/json";
    }

    const response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers,
        credentials: "include"
    });

    let result = {};

    try {
        result = await response.json();
    } catch {
        // A resposta pode não conter JSON.
    }

    if (!response.ok || result.success === false) {
        throw new Error(
            result.message || "Não foi possível concluir a solicitação."
        );
    }

    return result;
}

function showMessage(form, message, isError = false) {
    let messageElement = form.querySelector(".settings-message");

    if (!messageElement) {
        messageElement = document.createElement("p");
        messageElement.className = "settings-message";
        messageElement.setAttribute("role", "status");
        form.appendChild(messageElement);
    }

    messageElement.textContent = message;
    messageElement.style.color = isError ? "#b91c1c" : "#15803d";
}

// -------------------------
// Perfil
// -------------------------

let originalName = "";
let originalEmail = "";

function updateProfileDisplay(name, email) {
    if (userNameElement) {
        userNameElement.textContent = name || "Usuário";
    }

    if (profileNameElement) {
        profileNameElement.textContent = name || "Usuário";
    }

    if (profileEmailElement) {
        profileEmailElement.textContent = email || "E-mail não informado";
    }
}

function setProfileEditing(isEditing) {
    nameInput.disabled = !isEditing;
    emailInput.disabled = !isEditing;

    editProfileButton.hidden = isEditing;
    cancelProfileButton.hidden = !isEditing;
    saveProfileButton.disabled = !isEditing;
}

function restoreProfileValues() {
    nameInput.value = originalName;
    emailInput.value = originalEmail;
}

const storedUser = getStoredUser();

originalName = storedUser.name || "";
originalEmail = storedUser.email || "";

nameInput.value = originalName;
emailInput.value = originalEmail;

updateProfileDisplay(originalName, originalEmail);
setProfileEditing(false);

editProfileButton.addEventListener("click", () => {
    setProfileEditing(true);
    nameInput.focus();
});

cancelProfileButton.addEventListener("click", () => {
    restoreProfileValues();
    setProfileEditing(false);
});

profileForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (saveProfileButton.disabled) {
        return;
    }

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();

    if (!name || !email) {
        showMessage(profileForm, "Preencha o nome e o e-mail.", true);
        return;
    }

    saveProfileButton.disabled = true;

    try {
        await apiRequest("/setting/user", {
            method: "PUT",
            body: JSON.stringify({ name, email })
        });

        const user = getStoredUser();
        user.name = name;
        user.email = email;

        localStorage.setItem("user", JSON.stringify(user));

        originalName = name;
        originalEmail = email;

        updateProfileDisplay(name, email);
        setProfileEditing(false);

        showMessage(profileForm, "Perfil atualizado com sucesso.");
    } catch (error) {
        saveProfileButton.disabled = false;
        showMessage(profileForm, error.message, true);
    }
});

// -------------------------
// Alterar senha
// -------------------------

passwordForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const current_password = currentPasswordInput.value;
    const new_password = newPasswordInput.value;
    const confirm_password = confirmPasswordInput.value;

    if (!current_password || !new_password || !confirm_password) {
        showMessage(passwordForm, "Preencha todos os campos de senha.", true);
        return;
    }

    if (new_password !== confirm_password) {
        showMessage(
            passwordForm,
            "A confirmação não corresponde à nova senha.",
            true
        );
        return;
    }

    const changePasswordButton = document.getElementById("change-password");
    changePasswordButton.disabled = true;

    try {
        await apiRequest("/setting/password", {
            method: "PUT",
            body: JSON.stringify({
                current_password,
                new_password,
                confirm_password
            })
        });

        passwordForm.reset();
        showMessage(passwordForm, "Senha alterada com sucesso.");
    } catch (error) {
        showMessage(passwordForm, error.message, true);
    } finally {
        changePasswordButton.disabled = false;
    }
});

// -------------------------
// Logout
// -------------------------

logoutButton.addEventListener("click", async () => {
    logoutButton.disabled = true;

    try {
        await apiRequest("/logout", {
            method: "POST"
        });

        clearSession();
        window.location.href = "Login.html";
    } catch (error) {
        logoutButton.disabled = false;
        alert(error.message);
    }
});

// -------------------------
// Excluir conta
// -------------------------

deleteAccountButton.addEventListener("click", async () => {
    const confirmed = confirm(
        "Tem certeza de que deseja excluir sua conta? Essa ação não pode ser desfeita."
    );

    if (!confirmed) {
        return;
    }

    deleteAccountButton.disabled = true;

    try {
        await apiRequest("/setting/user", {
            method: "DELETE"
        });

        clearSession();
        window.location.href = "Login.html";
    } catch (error) {
        deleteAccountButton.disabled = false;
        alert(error.message);
    }
});