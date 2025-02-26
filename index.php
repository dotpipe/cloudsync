<?php

// Load configuration from .conf file
$config = parse_ini_file('deploy.conf');

// Get selected host from request
$selected_host = isset($_GET['host']) ? $_GET['host'] : $config['default_host'];

// Get API token for the selected app
$api_token = isset($config['cpanel_token']) ? $config['cpanel_token'] : 'No API Token Available';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CloudSync Deployment & API Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Two-column layout */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 300px;
            background: #fff;
            padding: 15px;
            border-right: 1px solid #ddd;
        }

        .sidebar h2 {
            font-size: 1.2em;
            margin-top: 0;
        }

        .sidebar select,
        .sidebar button {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .main-content {
            flex: 1;
            padding: 15px;
            background: #fff;
        }

        /* Tabs */
        .tabs {
            display: flex;
            margin-bottom: 10px;
        }

        .tab {
            padding: 10px 15px;
            background-color: #e0e0e0;
            margin-right: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: #007bff;
            color: #fff;
        }

        .tab-content {
            display: none;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            background: #fff;
        }

        .tab-content.active {
            display: block;
        }

        .button-container {
    display: flex;
    justify-content: space-between; /* Aligns buttons with space between them */
    align-items: center; /* Aligns them on the same horizontal level */
  }

  .open-login, .copy-api-token {
    padding: 10px 20px;
    margin: 5px;
  }

        #apiTokenContainer {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #f4f4f4;
            border-radius: 5px;
        }

        #apiToken {
            font-family: monospace;
            background: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            margin-right: 10px;
            user-select: all;
        }

        #copyToken {
            cursor: pointer;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
        }

        /* Table styling */
        #api-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        #api-table th,
        #api-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 350px;
        }

        #addConnectionModal label {
            display: block;
            margin-top: 8px;
        }

        #addConnectionModal input {
            width: 100%;
            padding: 6px;
            margin: 4px 0;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar: Host & App Selection -->
        <div class="sidebar">
            <h2>Host Selector</h2>
            <div id="host-view">
                <label for="hostSelect">Select Host:</label>
                <select id="hostSelect">
                    <option value="">-- Select Host --</option>
                    <option value="aws">AWS</option>
                    <option value="azure">Azure</option>
                    <option value="bluehost">Bluehost</option>
                    <option value="godaddy">GoDaddy</option>
                    <option value="hostgator">HostGator</option>
                    <option value="ionos">IONOS</option>
                    <option value="siteground">SiteGround</option>
                </select>
                <button id="loadAppsBtn">Select Apps for this Host</button>
            </div>
            <div id="app-view" style="display:none;">
                <label for="appSelect">Select App:</label>
                <select id="appSelect">
                    <option value="">-- Select App --</option>
                </select>
                <button id="backToHostBtn">Back</button>
                <button id="createAppBtn">Create New App</button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <!-- Mode Control -->
            <div style="margin-bottom:10px;">
                <label for="mode-select">Mode:</label>
                <select id="mode-select">
                    <option value="dev">Development Mode</option>
                    <option value="prod">Production Mode</option>
                </select>
            </div>
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" data-tab="api-settings">API & Settings</div>
                <div class="tab" data-tab="modala-editor-content">Modala Editor</div>
                <div class="tab" data-tab="preview-panel">Preview Panel</div>
            </div>
            <div class="button-container">
                <button id="loginBtn">Open Login</button>
                <span id="apiToken"><?php echo htmlspecialchars($api_token); ?></span>
                <button id="copyToken">Copy</button>
            </div>
            <!-- API & Settings Tab -->
            <div id="api-settings" class="tab-content active">
                <h3>API & Settings</h3>
                <button id="addConnectionBtn">Add New Connection</button>
                <table id="api-table">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Domain</th>
                            <th>Endpoint</th>
                            <th>File Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="api-table-body">
                        <!-- Rows populated via JS -->
                    </tbody>
                </table>
            </div>
            <!-- Modala Editor Tab -->
            <div id="modala-editor-content" class="tab-content">
                <h3>Modala Editor</h3>
                <!-- Example HTML Structure -->
                <textarea id="modala-editor" placeholder="Enter JSON for modal..."></textarea>
                
                <br>
                <button onclick="saveConfig()">Save Config</button>
                <button onclick="deployConfig()">Deploy Config</button>
                <button id="generate-modal">Generate Modal</button>
                <div id="modala-preview"></div>
            </div>
            <!-- Preview Panel Tab -->
            <div id="preview-panel" class="tab-content">
                <h3>HTML Preview</h3>
                <button onclick="openPreviewTab()">Open Live Preview</button>
                <div id="html-preview"></div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h3>Login</h3>
            <form id="loginForm">
                <label for="username">Username:</label>
                <input type="text" id="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" required>
                <br>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <!-- Add Connection Modal -->
    <div id="addConnectionModal" class="modal">
        <div class="modal-content">
            <h3>Add Connection Info</h3>
            <form id="addConnectionForm">
                <label for="connProvider">Provider:</label>
                <input type="text" id="connProvider" name="provider" required>
                <label for="connDomain">Domain:</label>
                <input type="text" id="connDomain" name="domain" required>
                <label for="connToken">API Token:</label>
                <input type="text" id="connToken" name="token">
                <label for="connEndpoint">Endpoint:</label>
                <input type="text" id="connEndpoint" name="endpoint" placeholder="https://...">
                <label for="connFileLocation">File Location:</label>
                <input type="text" id="connFileLocation" name="fileLocation" placeholder="/var/www/html/...">
                <label for="connStatus">Status:</label>
                <input type="text" id="connStatus" name="status" value="Connected">
                <br>
                <button type="submit">Add Connection</button>
                <button type="button" id="closeConnectionModal">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Utility selectors
        const $ = (sel) => document.querySelector(sel);
        const $$ = (sel) => document.querySelectorAll(sel);
        const baseUrl = "http://localhost:8080/CloudSync/";
        let selectedConfigPath = null;

        document.getElementById("generate-modal").addEventListener("click", function() {
            // Step 1: Get the JSON content from the modala-editor
            const editorContent = document.getElementById("modala-editor").value;

            // Step 2: Try to parse the content as JSON
            let jsonValue;
            try {
                jsonValue = JSON.parse(editorContent); // Assuming the editor contains valid JSON
            } catch (e) {
                console.error("Invalid JSON:", e);
                return; // Stop if JSON is invalid
            }

            // Step 3: Call the modala function with the parsed JSON and render in preview panel
            const previewPanel = document.getElementById("modala-preview");

            // Clear the preview panel before appending new content
            previewPanel.innerHTML = ""; 

            // Call the modala function
            modala(jsonValue, previewPanel);
        });

        document.getElementById("copyToken").addEventListener("click", function() {
            const tokenElement = document.getElementById("apiToken");
            navigator.clipboard.writeText(tokenElement.textContent).then(() => {
                alert("API Token copied!");
            }).catch(err => {
                console.error("Error copying token:", err);
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            fetch("getHostList.php")
                .then(response => response.json())
                .then(hosts => {
                    const hostSelect = document.getElementById("hostSelect");
                    hostSelect.innerHTML = ""; // Clear existing options

                    hosts.forEach(host => {
                        let option = document.createElement("option");
                        option.value = host;
                        option.textContent = host;
                        hostSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error fetching host list:", error));
        });

        /* ---------- Tabs ---------- */
        function initTabs() {
            const tabs = $$(".tab");
            const tabContents = $$(".tab-content");
            tabs.forEach(tab => {
                tab.addEventListener("click", () => {
                    tabs.forEach(t => t.classList.remove("active"));
                    tabContents.forEach(c => c.classList.remove("active"));
                    tab.classList.add("active");
                    $("#" + tab.dataset.tab).classList.add("active");
                });
            });
        }

        /* ---------- Host & App Selection ---------- */
        function initHostAppSelection() {
            const hostSelect = $("#hostSelect");
            const loadAppsBtn = $("#loadAppsBtn");
            const hostView = $("#host-view");
            const appView = $("#app-view");
            const appSelect = $("#appSelect");
            const backToHostBtn = $("#backToHostBtn");
            const createAppBtn = $("#createAppBtn");

            loadAppsBtn.addEventListener("click", () => {
                const host = hostSelect.value;
                if (!host) {
                    alert("Please select a host.");
                    return;
                }
                fetch(`getapps.php?host=${host}`)
                    .then(res => res.json())
                    .then(data => {
                        appSelect.innerHTML = '<option value="">-- Select App --</option>';
                        if (data.error) {
                            alert(data.error);
                        } else {
                            data.forEach(app => {
                                const opt = document.createElement("option");
                                opt.value = app;
                                opt.textContent = app;
                                appSelect.appendChild(opt);
                            });
                            hostView.style.display = "none";
                            appView.style.display = "block";
                        }
                    })
                    .catch(err => console.error("Error fetching apps:", err));
            });

            backToHostBtn.addEventListener("click", () => {
                hostView.style.display = "block";
                appView.style.display = "none";
                appSelect.innerHTML = '<option value="">-- Select App --</option>';
            });

            createAppBtn.addEventListener("click", () => {
                const host = hostSelect.value;
                const appName = prompt("Enter the name of the new app:");
                if (!host || !appName) {
                    alert("Please select a host and enter an app name.");
                    return;
                }
                const formData = new FormData();
                formData.append("host", host);
                formData.append("app_name", appName);
                fetch("createapp.php", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.text())
                    .then(result => {
                        alert(result);
                        // Refresh app list after creation
                        loadAppsBtn.click();
                    })
                    .catch(err => console.error("Error creating new app:", err));
            });
        }

        /* ---------- Mode & Connection Info ---------- */
        $("#mode-select").addEventListener("change", function () {
            const mode = this.value;
            if (mode === "prod") {
                $("#main-content").style.minHeight = "100vh";
            } else {
                $("#main-content").style.minHeight = "auto";
            }
            loadConnectionInfo(mode);
        });

        function loadConnectionInfo(mode) {
            const host = $("#hostSelect").value;
            if (!host) return;
            fetch(`getConnectionInfo.php?mode=${mode}&host=${host}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = $("#api-table-body");
                    tbody.innerHTML = "";
                    data.forEach(item => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `<td>${item.provider}</td>
                          <td>${item.domain}</td>
                          <td>${item.token}</td>
                          <td>${item.endpoint}</td>
                          <td>${item.fileLocation}</td>
                          <td>${item.status}</td>`;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => console.error("Error loading connection info:", err));
        }
        // Initial load in development mode
        loadConnectionInfo("dev");

        /* ---------- Add Connection Modal ---------- */
        function initAddConnectionModal() {
            const addConnBtn = document.getElementById("addConnectionBtn");
            const modal = document.getElementById("addConnectionModal");
            const closeModal = document.getElementById("closeConnectionModal");
            const form = document.getElementById("addConnectionForm");
            const hostSelect = document.getElementById("hostSelect");
            const providerSelect = document.getElementById("connProvider");

            addConnBtn.addEventListener("click", () => {
                if (hostSelect.value) {
                    providerSelect.value = hostSelect.value;
                } else {
                    providerSelect.value = "";
                }
                providerSelect.disabled = true; // Ensure it remains uneditable
                modal.style.display = "flex";
            });

            closeModal.addEventListener("click", () => {
                modal.style.display = "none";
            });

            window.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });

            form.addEventListener("submit", (e) => {
                e.preventDefault();
                const host = hostSelect.value;
                if (!host) {
                    alert("No host selected.");
                    return;
                }
                const formData = new FormData(form);
                fetch("addConnectionInfo.php?host=" + encodeURIComponent(host), {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(result => {
                        if (result.error) {
                            alert("Error: " + result.error);
                        } else {
                            alert("Connection info added successfully.");
                            loadConnectionInfo(document.getElementById("mode-select").value);
                        }
                    })
                    .catch(err => {
                        console.error("Error adding connection info:", err);
                        alert("Error adding connection info.");
                    })
                    .finally(() => {
                        modal.style.display = "none";
                        form.reset();
                    });
            });
        }

        /* ---------- Login Modal ---------- */
        function initLoginModal() {
            const loginBtn = $("#loginBtn");
            const loginModal = $("#loginModal");
            const loginForm = $("#loginForm");
            loginBtn.addEventListener("click", () => {
                loginModal.style.display = "flex";
            });
            window.addEventListener("click", (e) => {
                if (e.target === loginModal) {
                    loginModal.style.display = "none";
                }
            });
            loginForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const username = $("#username").value;
                const password = $("#password").value;
                const provider = $("#hostSelect").value;
                const appName = $("#appSelect").value;
                const loginURL = `${baseUrl}${provider}/${appName}/login`;
                fetch(loginURL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ username, password })
                })
                    .then(res => res.json())
                    .then(result => {
                        alert(result.success ? "Login successful!" : "Login failed. Check credentials.");
                    })
                    .catch(err => {
                        console.error("Login error:", err);
                        alert("Error during login.");
                    })
                    .finally(() => {
                        loginModal.style.display = "none";
                    });
            });
        }

        /* ---------- Preview & Config Functions ---------- */
        function openPreviewTab() {
            const provider = $("#hostSelect").value;
            const appName = $("#appSelect").value;
            if (!provider || !appName) {
                alert("Please select both a host and an app to preview.");
                return;
            }
            const previewURL = `${baseUrl}${provider}/${appName}/`;
            window.open(previewURL, "_blank");
        }
        function saveConfig() {
            if (!selectedConfigPath) {
                alert("Select a config file to save!");
                return;
            }
            const content = $("#modala-editor").value;
            const provider = $("#hostSelect").value;
            const fullPath = `${baseUrl}${provider}/${selectedConfigPath}`;
            console.log("Saving config to", fullPath, "Content:", content);
            $("#api-results").textContent = `Simulated save of ${selectedConfigPath}\nContent:\n${content}`;
        }
        function deployConfig() {
            if (!selectedConfigPath) {
                alert("Select a config file to deploy!");
                return;
            }
            const content = $("#modala-editor").value;
            const provider = $("#hostSelect").value;
            const mode = $("#mode-select").value;
            $("#api-results").textContent = `Simulated deployment of ${selectedConfigPath} to ${provider}/${mode}\nContent:\n${content}`;
            console.log("Deploying to", `${baseUrl}${provider}/${selectedConfigPath}`, "Content:", content);
        }
        function previewModala() {
            let text = $("#modala-editor").value;
            try {
                const jsonData = JSON.parse(text);
                $("#modala-preview").innerHTML = "";
                modala(jsonData, "modala-preview"); // Your modala preview function
            } catch (e) {
                console.error("Invalid JSON in modala editor:", e);
            }
        }
        function loadConfigContent(path) {
            const provider = $("#hostSelect").value;
            const fullPath = `${baseUrl}${provider}/${path}`;
            console.log("Loading config content for:", path, "Full URL:", fullPath);
            getTextFile(fullPath)
                .then(data => {
                    $("#modala-editor").value = data;
                    showContent("modala-editor-content");
                })
                .catch(e => {
                    console.error("Error loading config:", e.message);
                    $("#modala-editor").value = "Error loading file: " + e.message;
                });
        }
        function showContent(contentId) {
            $$(".content").forEach(el => el.classList.remove("active"));
            const target = document.getElementById(contentId);
            if (target) target.classList.add("active");
            $$(".tab").forEach(tab => tab.classList.remove("active"));
            const activeTab = document.querySelector(`.tab[data-tab="${contentId}"]`);
            if (activeTab) activeTab.classList.add("active");
        }

        // --- Document Ready ---
        document.addEventListener("DOMContentLoaded", () => {
            initTabs();
            initHostAppSelection();
            initLoginModal();
            initAddConnectionModal();
        });
    </script>
</body>

</html>