
// Server-time based countdown timer with improved UI
$(document).ready(function() {
    // Initialize variables (serverTimeOffset is declared globally in server-time.js)
    let isTimerInitialized = false;
    let forceShowTimer = false;
    
    // Initialize timer based on server time
    initializeServerTime();
    
    // Set up the force show timer button for admins (if exists)
    $('#force-show-timer').on('click', function() {
        forceShowTimer = true;
        initializeServerTime();
    });
    
    // Get and set up server time
    async function initializeServerTime() {
        try {
            // Try to get server time
            const serverTimeStr = await window.getServerTime();
            if (!serverTimeStr) {
                throw new Error('Failed to get server time');
            }
            
            // Parse server time (serverTimeOffset is already calculated by server-time.js)
            const serverTime = new Date(serverTimeStr);
            
            // Calculate next midnight on server time
            const nextResetTime = new Date(serverTime);
            nextResetTime.setHours(24, 0, 0, 0);
            
            // Check if timer should be shown based on remaining views data attribute
            const timerElement = $('#countdown-timer');
            const remainingViews = parseInt(timerElement.data('remaining-views') || 0);
            
            if (remainingViews <= 0 || forceShowTimer) {
                // Show timer if daily limit reached or forced
                timerElement.show();
                startServerTimeCountdown(nextResetTime);
                updateProgressBar(nextResetTime);
            } else {
                // Hide timer if user still has videos available
                timerElement.hide();
            }
            
            isTimerInitialized = true;
        } catch (error) {
            fallbackToLocalTimer();
        }
    }
    
    // Start the server-time based countdown
    function startServerTimeCountdown(nextResetTime) {
        // Make sure the timer is visible
        $('#countdown-timer').show();
        
        function updateTimer() {
            // Get current time using server offset
            const now = new Date();
            const adjustedNow = new Date(now.getTime() + serverTimeOffset);
            
            // Calculate difference in seconds until next reset
            const diff = Math.floor((nextResetTime - adjustedNow) / 1000);
            
            if (diff <= 0) {
                // Time's up - reload page to get new videos
                $('#timer-display').text('00:00:00');
                setTimeout(() => location.reload(), 1500);
                return;
            }
            
            // Format time
            const hours = String(Math.floor(diff / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const seconds = String(diff % 60).padStart(2, '0');
            
            // Update display
            $('#timer-display').text(`${hours}:${minutes}:${seconds}`);
            
            // Update progress bar every second
            updateProgressBarWidth(diff, nextResetTime);
            
            // Run again in 1 second
            setTimeout(updateTimer, 1000);
        }
        
        // Start countdown immediately
        updateTimer();
    }
    
    // Set up the initial progress bar
    function updateProgressBar(nextResetTime) {
        const now = new Date();
        const adjustedNow = new Date(now.getTime() + serverTimeOffset);
        const totalSeconds = 24 * 60 * 60; // Seconds in a day
        const remainingSeconds = Math.floor((nextResetTime - adjustedNow) / 1000);
        const percentComplete = 100 - ((remainingSeconds / totalSeconds) * 100);
        
        $('#timer-progress').css('width', percentComplete + '%');
    }
    
    // Update progress bar width during countdown
    function updateProgressBarWidth(remainingSeconds, nextResetTime) {
        const totalSeconds = 24 * 60 * 60; // Seconds in a day
        const percentComplete = 100 - ((remainingSeconds / totalSeconds) * 100);
        $('#timer-progress').css('width', percentComplete + '%');
    }
    
    // Fallback to local time if server time fails
    function fallbackToLocalTimer() {
        // Check if timer should be shown based on remaining views data attribute
        const timerElement = $('#countdown-timer');
        const remainingViews = parseInt(timerElement.data('remaining-views') || 0);
        
        if (remainingViews <= 0 || forceShowTimer) {
            // Show the timer if user has reached daily limit or forced
            timerElement.show();
            startLocalTimeCountdown();
        } else {
            // Hide timer if user still has videos available
            timerElement.hide();
        }
    }
    
    // Start a local-time based countdown as fallback
    function startLocalTimeCountdown() {
        function updateLocalTimer() {
            // Get current time
            const now = new Date();
            
            // Set target time to next midnight
            const tomorrow = new Date(now);
            tomorrow.setHours(24, 0, 0, 0);
            
            // Calculate difference in seconds
            const diff = Math.floor((tomorrow - now) / 1000);
            
            if (diff <= 0) {
                // Time's up - reload page to get new videos
                $('#timer-display').text('00:00:00');
                setTimeout(() => location.reload(), 1500);
                return;
            }
            
            // Format time
            const hours = String(Math.floor(diff / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const seconds = String(diff % 60).padStart(2, '0');
            
            // Update display
            $('#timer-display').text(`${hours}:${minutes}:${seconds}`);
            
            // Update progress bar
            const totalSeconds = 24 * 60 * 60; // Seconds in a day
            const percentComplete = 100 - ((diff / totalSeconds) * 100);
            $('#timer-progress').css('width', percentComplete + '%');
            
            // Run again in 1 second
            setTimeout(updateLocalTimer, 1000);
        }
        
        // Start the local timer immediately
        updateLocalTimer();
    }
});
