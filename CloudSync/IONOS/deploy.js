const ftp = require('basic-ftp');
const fs = require('fs');

// Load Ionos FTP config
const config = require('./provider-config.json');

const client = new ftp.Client();

async function uploadFile(filePath, remotePath) {
  try {
    await client.access({
      host: config.ftpHost,
      user: config.ftpUsername,
      password: config.ftpPassword,
      port: config.ftpPort
    });
    await client.uploadFrom(filePath, remotePath);
    console.log("File uploaded to Ionos FTP:", remotePath);
  } catch (err) {
    console.error("Error uploading file to Ionos FTP:", err);
  } finally {
    client.close();
  }
}
