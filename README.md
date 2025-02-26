# Modala & dotPipe.js Cloud Deployment  

## Overview  
This project automates the deployment of structured HTML, JSON, and dependency files to cloud-based server services. Using \`dotPipe.js\`, it converts modala-based UI elements into JSON, structures dependencies, and ensures a clean and dynamic web deployment system.  

## Features  
- **HTML-to-JSON Conversion**: Converts HTML structures into uniquely formatted JSON files with enforced IDs and parent-child relationships.  
- **Cloud Deployment**: Automates file uploads to server services such as AWS, Firebase, or DigitalOcean.  
- **Dependency Management**: Establishes connections between elements and their required resources.  
- **Class & Style Mapping**: Extracts styles into external CSS files while maintaining proper class structures in JSON.  
- **Live Template Editing**: Allows users to modify and save UI components dynamically.  

## File Structure  
\`\`\`
/project-root
│── index.html          # Main interface  
│── script.js           # Handles HTML-to-JSON conversion & deployments  
│── dotPipe.js          # Manages structure and dependencies  
│── styles.css          # Stores extracted styles  
│── dependencies.json   # Tracks component relationships  
│── modala-templates/   # Saved modala template structures  
│── cloud-config.json   # Configuration for cloud services  
│── deploy.js           # Automates cloud uploads  
│── README.md           # Project documentation  
\`\`\`  

## JSON Structure  
- Each object’s **first key** is a **unique identifier** (usually the element’s ID).  
- **Tag names are properties**, not object keys.  
- **Attributes** such as \`id\`, \`class\`, and \`textContent\` are stored directly.  
- **Nested elements** are stored as objects with meaningful key names.  

### Example JSON Output  
\`\`\`json
{
  "modal-example": {
    "id": "modal-example",
    "tagname": "div",
    "class": "new-class-name existing-class",
    "p-content": {
      "id": "p-1",
      "tagname": "p",
      "textContent": "This is a modal",
      "class": "modal-text",
      "style": "color: red;"
    },
    "close-button": {
      "id": "btn-close",
      "tagname": "button",
      "textContent": "Close",
      "class": "close-button"
    }
  }
}
\`\`\`

## Deployment Process  
1. **Configure Cloud Settings**  
   - Edit \`cloud-config.json\` to specify cloud service credentials and endpoints.  

2. **Prepare Files**  
   - Run \`script.js\` to convert modala HTML structures into JSON and CSS.  

3. **Deploy to Cloud**  
   - Run \`deploy.js\` to upload structured files to the cloud.  
   - Supports AWS S3, GoDaddy Hosting, and BlueHost Spaces.  

### Example `cloud-config.json`  
\`\`\`json
{
  "service": "aws",
  "bucket": "my-cloud-storage",
  "region": "us-west-2",
  "accessKeyId": "YOUR_ACCESS_KEY",
  "secretAccessKey": "YOUR_SECRET_KEY"
}
\`\`\`

## Future Improvements  
- Implement UI-based cloud service selection.  
- Enhance deployment logs & error handling.  
- Support real-time sync with cloud storage.  
