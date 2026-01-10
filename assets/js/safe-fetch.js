/**
 * Production-Safe Fetch API Wrapper
 * 
 * Handles non-JSON responses, network errors, and provides detailed logging
 * Use this instead of raw fetch() for all API calls
 */

const SafeFetch = {
    /**
     * Make a POST request with automatic error handling
     * 
     * @param {string} url - API endpoint URL
     * @param {FormData|Object} data - Request data
     * @param {Object} options - Additional options
     * @returns {Promise<Object>} - Parsed JSON response
     */
    async post(url, data, options = {}) {
        const config = {
            method: 'POST',
            ...options
        };
        
        // Handle FormData vs regular object
        if (data instanceof FormData) {
            config.body = data;
        } else {
            config.body = new URLSearchParams(data);
        }
        
        return this.request(url, config);
    },
    
    /**
     * Make a GET request with automatic error handling
     */
    async get(url, options = {}) {
        return this.request(url, { method: 'GET', ...options });
    },
    
    /**
     * Core request handler with comprehensive error handling
     */
    async request(url, config = {}) {
        let response;
        let responseText = '';
        
        try {
            console.log(`[SafeFetch] ${config.method || 'GET'} ${url}`);
            
            // Make the request
            response = await fetch(url, config);
            
            // Get raw response text first (for debugging)
            responseText = await response.text();
            
            // Log raw response for debugging
            console.log('[SafeFetch] Raw Response:', responseText.substring(0, 500));
            
            // Check if response is empty
            if (!responseText || responseText.trim() === '') {
                throw new Error('Server returned empty response (possible 500 error or crash)');
            }
            
            // Try to parse as JSON
            let jsonData;
            try {
                jsonData = JSON.parse(responseText);
            } catch (parseError) {
                // Not valid JSON - server likely returned HTML error page
                console.error('[SafeFetch] JSON Parse Error:', parseError);
                console.error('[SafeFetch] Response Text:', responseText);
                
                // Check if it's an HTML error page
                if (responseText.includes('<!DOCTYPE') || responseText.includes('<html')) {
                    throw new Error('Server returned HTML instead of JSON (500 error or configuration issue)');
                }
                
                throw new Error('Server response is not valid JSON: ' + parseError.message);
            }
            
            // Check HTTP status code
            if (!response.ok) {
                const errorMsg = jsonData.message || `HTTP ${response.status}: ${response.statusText}`;
                const error = new Error(errorMsg);
                error.status = response.status;
                error.data = jsonData;
                throw error;
            }
            
            // Check if API indicates success
            if (jsonData.success === false) {
                const error = new Error(jsonData.message || 'API request failed');
                error.data = jsonData;
                throw error;
            }
            
            // Log success
            console.log('[SafeFetch] Success:', jsonData);
            
            return jsonData;
            
        } catch (error) {
            // Enhanced error logging
            console.error('[SafeFetch] Request Failed:', {
                url,
                method: config.method || 'GET',
                error: error.message,
                status: response?.status,
                statusText: response?.statusText,
                responsePreview: responseText.substring(0, 500)
            });
            
            // Create user-friendly error message
            let userMessage = 'An error occurred. Please try again.';
            
            if (!response) {
                userMessage = 'Network error. Please check your internet connection.';
            } else if (error.message.includes('HTML instead of JSON')) {
                userMessage = 'Server configuration error. Please contact support.';
            } else if (error.message.includes('empty response')) {
                userMessage = 'Server error. Please try again or contact support.';
            } else if (error.data?.message) {
                userMessage = error.data.message;
            } else if (error.message) {
                userMessage = error.message;
            }
            
            // Re-throw with enhanced error info
            const enhancedError = new Error(userMessage);
            enhancedError.originalError = error;
            enhancedError.response = response;
            enhancedError.responseText = responseText;
            enhancedError.data = error.data;
            
            throw enhancedError;
        }
    }
};

// Export for use in other files (if using modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SafeFetch;
}
