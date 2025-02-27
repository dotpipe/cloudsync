function generateFileTree(hostName, appName) {
    const baseDir = `CloudSync/${hostName}/${appName}`;
    const tree = {
        label: appName,
        a01: [
            { label: 'dev', b01: [] },
            { label: 'prod', b02: [] }
        ]
    };

    function addFilesToTree(dir, node) {
        const files = fs.readdirSync(dir);
        files.forEach((file, index) => {
            const filePath = path.join(dir, file);
            const stats = fs.statSync(filePath);
            const key = `c${String(index + 1).padStart(2, '0')}`;
            if (stats.isDirectory()) {
                const childNode = { label: file, [key]: [] };
                node[Object.keys(node)[1]].push(childNode);
                addFilesToTree(filePath, childNode);
            } else {
                node[Object.keys(node)[1]].push({
                    label: file,
                    ajax: filePath.replace(baseDir, ''),
                    insert: "modala-editor",
                    "tool-tip": stats.mtime.toISOString()
                });
            }
        });
    }

    addFilesToTree(`${baseDir}/dev`, tree.a01[0]);
    addFilesToTree(`${baseDir}/prod`, tree.a01[1]);

    return tree;
}

function deleteFile(filePath) {
    fetch(filePath, { method: 'DELETE' })
        .then(() => updateTreeView());
}

function moveFile(oldPath, newPath) {
    fetch('/move-file', {
        method: 'POST',
        body: JSON.stringify({ oldPath, newPath }),
        headers: { 'Content-Type': 'application/json' }
    }).then(() => updateTreeView());
}

function updateTreeView() {
    const currentHostName = document.getElementById("hostSelect").value;
    const currentAppName = document.getElementById("appSelect").value;
    createTreeView(currentHostName, currentAppName);
}

document.getElementById('mode-toggle').addEventListener('change', function () {
    const mode = this.checked ? 'prod' : 'dev';
    console.log('Mode switched to:', mode);
    // Add your logic here to handle mode switching
});

let currentFilePath = null;

function updateSaveButton(filePath) {
    currentFilePath = filePath;
    const saveButton = document.getElementById('save-button');
    saveButton.onclick = () => saveFile(filePath);
}

function saveFile(filePath) {
    const content = dotPipe.getEditorContent();
    fetch(filePath, {
        method: 'PUT',
        body: content
    }).then(() => {
        updateTreeView();
    });
}

let stashedContent = null;

function stashFile(content) {
    stashedContent = content;
    updateStashDropdown();
}

function updateStashDropdown() {
    const dropdown = document.getElementById('stash-dropdown');
    dropdown.innerHTML = `
    <option value="saved">Saved</option>
    ${stashedContent ? '<option value="stashed">Stashed</option>' : ''}
  `;
}

document.getElementById('stash-dropdown').addEventListener('change', (e) => {
    if (e.target.value === 'stashed' && stashedContent) {
        dotPipe.setEditorContent(stashedContent);
    } else {
        loadFileToEditor(currentFilePath);
    }
});

function loadFileToEditor(filePath) {
    const currentContent = dotPipe.getEditorContent();
    if (currentContent) {
        stashFile(currentContent);
    }

    fetch(filePath)
        .then(response => response.text())
        .then(content => {
            dotPipe.setEditorContent(content);
            updateSaveButton(filePath);
        });
}

function createTreeView(hostName, appName) {
    const treeData = generateFileTree(hostName, appName);
    dotPipe.createTree('#left-panel', treeData, {
        onClick: (node) => {
            if (node.insert === "modala-editor") {
                loadFileToEditor(node.ajax);
            }
        }
    });
}

let currentContent = null;
let saveInterval = null;

function stashFile(content) {
    stashedContent = content;
    updateStashDropdown();
}

function updateStashDropdown() {
    const dropdown = document.getElementById('stash-dropdown');
    dropdown.innerHTML = `
        <option value="current">Current</option>
        <option value="saved">Last Saved</option>
        ${stashedContent ? '<option value="stashed">Stashed</option>' : ''}
    `;
}

function saveAndCleanup() {
    currentContent = dotPipe.getEditorContent();
    saveFile(currentFilePath, currentContent);
    
    if (stashedContent) {
        saveFile(currentFilePath + '.stash', stashedContent);
    }
    
    // Zip both files
    zipFiles(currentFilePath, [currentFilePath, currentFilePath + '.stash']);
}

function startSaveInterval() {
    if (saveInterval) clearInterval(saveInterval);
    saveInterval = setInterval(saveAndCleanup, 15 * 60 * 1000); // 15 minutes
}

document.getElementById('stash-dropdown').addEventListener('change', (e) => {
    switch(e.target.value) {
        case 'current':
            dotPipe.setEditorContent(currentContent);
            break;
        case 'saved':
            loadFileToEditor(currentFilePath);
            break;
        case 'stashed':
            dotPipe.setEditorContent(stashedContent);
            break;
    }
});

function loadFileToEditor(filePath) {
    fetch(filePath)
        .then(response => response.text())
        .then(content => {
            currentContent = content;
            dotPipe.setEditorContent(content);
            updateSaveButton(filePath);
            startSaveInterval();
        });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeFileTree();
    setupModeSwitch();
});

function initializeFileTree() {
    const hostName = getCurrentHostName();
    const appName = getCurrentAppName();
    createTreeView(hostName, appName);
}

function setupModeSwitch() {
    document.getElementById('mode-toggle').addEventListener('change', function() {
        const mode = this.checked ? 'prod' : 'dev';
        console.log('Mode switched to:', mode);
        updateTreeView();
    });
}

function loadFileToEditor(filePath) {
    const currentContent = getEditorContent();
    if (currentContent) {
        stashFile(currentContent);
    }

    fetch(filePath)
        .then(response => response.text())
        .then(content => {
            setEditorContent(content);
            updateSaveButton(filePath);
            startSaveInterval();
        });
}

function startSaveInterval() {
    if (saveInterval) clearInterval(saveInterval);
    saveInterval = setInterval(saveAndCleanup, 15 * 60 * 1000);
}

function saveAndCleanup() {
    currentContent = getEditorContent();
    saveFile(currentFilePath);
    
    if (stashedContent) {
        saveFile(currentFilePath + '.stash', stashedContent);
    }
    
    zipFiles(currentFilePath, [currentFilePath, currentFilePath + '.stash']);
}

document.getElementById('stash-dropdown').addEventListener('change', (e) => {
    switch(e.target.value) {
        case 'current':
            setEditorContent(currentContent);
            break;
        case 'saved':
            loadFileToEditor(currentFilePath);
            break;
        case 'stashed':
            setEditorContent(stashedContent);
            break;
    }
});
