/**
 * Read Laravel encrypted XSRF-TOKEN cookie for JSON / fetch requests.
 */
export function xsrfToken() {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);

    return match ? decodeURIComponent(match[1]) : '';
}
