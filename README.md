## Overview  
This project is designed to facilitate the conversion of HTML structures into JSON representations using \`dotPipe.js\`. The system manages dependencies, nested objects, and styles, ensuring structured data for manipulation and storage.  

## Features  
- **HTML to JSON Conversion**: Parses an HTML structure into a JSON format where the first key in an object is its unique identifier.  
- **ID Enforcement**: Every object includes an \`id\` property.  
- **Dependency Management**: Establishes parent-child relationships between elements.  
- **Class & Style Separation**: Ensures styles are extracted and assigned via \`.css\` files while maintaining class lists in the JSON structure.  
- **Editable Templates**: Allows objects to be saved and reused as templates.  
- **Dynamic Updates**: Ensures real-time changes are reflected in JSON format and stored accordingly.  

## File Structure  
\`\`\`
/project-root
│── index.html          # Main interface  
│── script.js           # Contains logic for HTML-to-JSON conversion  
│── dotPipe.js          # Custom library for managing data structures  
│── styles.css          # Holds extracted styles  
│── dependencies.json   # Stores relationships between components  
│── modala-templates/   # Folder for saved modala templates  
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

## Installation & Usage  
1. Clone the repository.  
2. Open \`index.html\` in a browser.  
3. Modify the Modala Editor and save templates.  
4. Use dotPipe.js to process dependencies and style extraction.  

## Future Improvements  
- Implement an interface for better visualization of dependencies.  
- Optimize performance for handling large HTML structures.  
- Allow inline style-to-CSS mapping automation. 