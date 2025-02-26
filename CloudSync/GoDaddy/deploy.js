const fetch = require('node-fetch');

// Load GoDaddy API config
const config = require('./provider-config.json');

const apiEndpoint = 'https://api.godaddy.com/v1/domains';

async function updateDomain(domainName, data) {
  const response = await fetch(`${apiEndpoint}/${domainName}`, {
    method: 'PATCH',
    headers: {
      'Authorization': `sso-key ${config.apiKey}:${config.apiSecret}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  });

  const result = await response.json();
  if (response.ok) {
    console.log("Domain updated successfully:", result);
  } else {
    console.error("Error updating domain:", result);
  }
}
