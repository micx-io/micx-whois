/**
 * Micx.io Whois Service
 *
 * Usage: See https://github.com/micx-io/micx-whois
 *
 * @licence MIT
 * @author Matthias Leuffen <m@tth.es>
 */

const MicxWhois = {
  attrs: {
    "service_id": "%%SERVICE_ID%%",
    "error": "%%ERROR%%",
      "tlds": JSON.parse('%%TLDS%%'),
    "endpoint_url": "%%ENDPOINT_URL%%",
    "debug": false
  },
  query: async (domain) => {
    let resp = await fetch(MicxWhois.attrs.endpoint_url + "&q=" + encodeURIComponent(domain));
    return resp.json();
  }
}
