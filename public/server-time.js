/**
 * Server Time Utilities
 * 
 * This module provides functions for getting and using the server time,
 * which is useful for time-sensitive operations that should be based on
 * the server's clock rather than the client's.
 */

// Cached values
let serverTimeOffset = null;
let lastFetchTime = null;

/**
 * Fetches the current server time from the API
 * @returns {Promise<string>} A promise that resolves to the server time string
 */
window.getServerTime = async function() {
    try {
        const response = await fetch('/api/server-time');
        if (!response.ok) throw new Error('Failed to fetch server time');
        const data = await response.json();
        
        // Calculate and store the offset
        const serverTime = new Date(data.server_time);
        const clientTime = new Date();
        serverTimeOffset = serverTime.getTime() - clientTime.getTime();
        lastFetchTime = clientTime.getTime();
        
        return data.server_time;
    } catch (e) {
        console.error('Error fetching server time:', e);
        return null;
    }
};

/**
 * Gets the current server time by using the stored offset
 * @returns {Date} The current server time as a Date object
 */
window.getCurrentServerTime = function() {
    // If we don't have an offset yet, return the client time
    if (serverTimeOffset === null) {
        return new Date();
    }
    
    // Use the stored offset to calculate the current server time
    const now = new Date();
    return new Date(now.getTime() + serverTimeOffset);
};

/**
 * Gets the time until the next server midnight
 * @returns {Object} An object with hours, minutes, seconds until server midnight
 */
window.getTimeUntilServerMidnight = function() {
    // Get the current server time
    const serverNow = window.getCurrentServerTime();
    
    // Calculate the next midnight on server time
    const serverMidnight = new Date(serverNow);
    serverMidnight.setHours(24, 0, 0, 0);
    
    // Calculate the difference in seconds
    const diffSeconds = Math.floor((serverMidnight - serverNow) / 1000);
    
    // Calculate hours, minutes, seconds
    const hours = Math.floor(diffSeconds / 3600);
    const minutes = Math.floor((diffSeconds % 3600) / 60);
    const seconds = diffSeconds % 60;
    
    return {
        hours: hours,
        minutes: minutes,
        seconds: seconds,
        totalSeconds: diffSeconds,
        formatted: `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
    };
};
