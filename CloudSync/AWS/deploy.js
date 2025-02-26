const AWS = require('aws-sdk');
const fs = require('fs');

// Load AWS credentials from file
const credentials = require('./aws-config.json');
AWS.config.update({
  accessKeyId: credentials.accessKeyId,
  secretAccessKey: credentials.secretAccessKey,
  region: credentials.region
});

// Define deploy logic for AWS (example: S3 upload)
const s3 = new AWS.S3();

const uploadFile = (filePath, bucketName, key) => {
  const fileContent = fs.readFileSync(filePath);
  const params = {
    Bucket: bucketName,
    Key: key,
    Body: fileContent
  };

  s3.upload(params, (err, data) => {
    if (err) {
      console.error("Error uploading file to AWS S3:", err);
    } else {
      console.log("File uploaded successfully to AWS S3:", data.Location);
    }
  });
};

