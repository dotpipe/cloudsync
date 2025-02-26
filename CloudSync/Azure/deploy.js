const azure = require('azure-storage');
const fs = require('fs');

// Load Azure credentials
const config = require('./provider-config.json');
const blobService = azure.createBlobService(config.accountName, config.accountKey);

// Upload file to Azure Storage
const uploadFile = (filePath, containerName, blobName) => {
  blobService.createBlockBlobFromLocalFile(containerName, blobName, filePath, (err, result, response) => {
    if (err) {
      console.error("Error uploading file to Azure:", err);
    } else {
      console.log("File uploaded successfully to Azure:", result);
    }
  });
};
