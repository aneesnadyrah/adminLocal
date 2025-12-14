<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

/**
 * Optimized Database Connection Factory
 * Handles network latency spikes and connection issues
 * Addresses the 441ms database server ping spikes
 */
class DBConnectionFactory {
    private $db; // Database connection object
    private static $instance = null; // Singleton instance
    private $connectionAttempts = 0;
    private $lastConnectionTime = 0;
    private $connectionStats = [];

    public function __construct() {
        $this->db = null;
    }

    /**
     * Singleton pattern to reuse connections
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Create optimized database connection with retry logic
     */
    public function createConnection() {
        // Return existing connection if valid
        if ($this->db && $this->isConnectionAlive()) {
            return $this->db;
        }

        // Create new connection with retry logic
        return $this->createConnectionWithRetry();
    }

    /**
     * Create connection with retry logic for network spikes
     */
    private function createConnectionWithRetry() {
        $system = new System();
        $DSN = $system->DBConnection;
        $maxRetries = 3;
        $baseTimeout = 10; // Base timeout in seconds
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $start = microtime(true);
            $timeout = $baseTimeout + ($attempt * 5); // Increase timeout with each retry
            
            try {
                // Enhanced PDO options for PostgreSQL network resilience
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_TIMEOUT => $timeout,
                    PDO::ATTR_PERSISTENT => true, // Use persistent connections
                    
                    // Connection pooling options
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                $this->db = new PDO($DSN, null, null, $options);
                
                // Set PostgreSQL session variables for optimization
                $this->db->exec("SET statement_timeout = '300s'");        // Query timeout
                $this->db->exec("SET lock_timeout = '30s'");              // Lock timeout  
                $this->db->exec("SET idle_in_transaction_session_timeout = '300s'"); // Idle timeout
                $this->db->exec("SET tcp_keepalives_idle = 300");         // TCP keepalive
                $this->db->exec("SET tcp_keepalives_interval = 30");      // TCP keepalive interval
                $this->db->exec("SET tcp_keepalives_count = 3");          // TCP keepalive count
                
                // Test connection with a simple query
                $testStart = microtime(true);
                $stmt = $this->db->query("SELECT 1");
                $testTime = (microtime(true) - $testStart) * 1000;
                
                $connectionTime = (microtime(true) - $start) * 1000;
                $this->lastConnectionTime = $connectionTime;
                $this->connectionAttempts++;
                
                // Log connection statistics
                $this->logConnectionStats($attempt, $connectionTime, $testTime, true);
                
                // Warning for slow connections
                if ($connectionTime > 200) {
                    error_log("SLOW DB CONNECTION: {$connectionTime}ms on attempt $attempt (query test: {$testTime}ms)");
                }
                
                return $this->db;
                
            } catch (PDOException $e) {
                $failedTime = (microtime(true) - $start) * 1000;
                $this->logConnectionStats($attempt, $failedTime, 0, false, $e->getMessage());
                
                error_log("DB connection attempt $attempt failed after {$failedTime}ms: " . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    // Exponential backoff: wait longer between retries
                    $waitTime = pow(2, $attempt) * 100000; // 200ms, 400ms, 800ms
                    usleep($waitTime);
                } else {
                    // Log final failure and throw exception
                    error_log("DB connection failed after $maxRetries attempts. Total time: " . 
                             array_sum(array_column($this->connectionStats, 'time')) . "ms");
                    throw new Exception("Database connection failed after $maxRetries attempts: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Check if existing connection is still alive
     */
    private function isConnectionAlive() {
        if (!$this->db) {
            return false;
        }

        try {
            $start = microtime(true);
            $stmt = $this->db->query("SELECT 1");
            $pingTime = (microtime(true) - $start) * 1000;
            
            // If ping takes too long, consider connection "dead"
            if ($pingTime > 1000) { // 1 second threshold
                error_log("Connection ping too slow: {$pingTime}ms - reconnecting");
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Connection health check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute query with retry logic
     */
    public function executeQuery($query, $params = []) {
        $maxRetries = 2;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $db = $this->createConnection();
                $start = microtime(true);
                
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                
                $queryTime = (microtime(true) - $start) * 1000;
                
                // Log slow queries
                if ($queryTime > 500) {
                    error_log("SLOW QUERY ({$queryTime}ms): " . substr($query, 0, 100) . "...");
                }
                
                return $stmt;
                
            } catch (Exception $e) {
                error_log("Query attempt $attempt failed: " . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    // Reset connection on failure
                    $this->db = null;
                    usleep(200000); // Wait 200ms before retry
                } else {
                    throw $e;
                }
            }
        }
    }

    /**
     * Log connection statistics for monitoring
     */
    private function logConnectionStats($attempt, $connectionTime, $testTime, $success, $error = null) {
        $this->connectionStats[] = [
            'timestamp' => time(),
            'attempt' => $attempt,
            'time' => $connectionTime,
            'test_time' => $testTime,
            'success' => $success,
            'error' => $error
        ];

        // Keep only last 50 connection attempts
        if (count($this->connectionStats) > 50) {
            array_shift($this->connectionStats);
        }
    }

    /**
     * Get connection statistics for monitoring
     */
    public function getConnectionStats() {
        $total = count($this->connectionStats);
        $successful = count(array_filter($this->connectionStats, function($stat) {
            return $stat['success'];
        }));
        
        $avgTime = $total > 0 ? array_sum(array_column($this->connectionStats, 'time')) / $total : 0;
        
        return [
            'total_attempts' => $total,
            'successful' => $successful,
            'success_rate' => $total > 0 ? ($successful / $total) * 100 : 0,
            'avg_connection_time' => round($avgTime, 2),
            'last_connection_time' => $this->lastConnectionTime,
            'recent_stats' => array_slice($this->connectionStats, -10) // Last 10 attempts
        ];
    }

    /**
     * Force connection reset (useful for testing)
     */
    public function resetConnection() {
        $this->db = null;
    }

    /**
     * Get health status
     */
    public function getHealthStatus() {
        $stats = $this->getConnectionStats();
        
        if ($stats['success_rate'] < 70) {
            return 'critical';
        } elseif ($stats['avg_connection_time'] > 500) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }
}

/**
 * Optimized FTP Connection Factory (unchanged but formatted)
 */
class FTPConnectionFactory {
    private $ftp;
    private $connect;

    public function __construct() {
        $this->ftp = null;
    }

    public function createConnection() {
        if (!$this->ftp) {
            try {
                $system = new System();
                $FTP = $system->FTPConnection;
                $this->connect = ftp_connect($FTP->host);

                if (!$this->connect) {
                    throw new Exception("FTP connection failed");
                }

                $this->ftp = ftp_login($this->connect, $FTP->username, $FTP->password);

                if (!$this->ftp) {
                    throw new Exception("FTP login failed");
                }
                
            } catch (Exception $e) {
                throw new Exception("FTP connection initialization failed: " . $e->getMessage());
            }
        }

        return $this->connect;
    }
}

/**
 * Static helper function for easy access
 */
class DB {
    public static function connection() {
        return DBConnectionFactory::getInstance()->createConnection();
    }
    
    public static function query($query, $params = []) {
        return DBConnectionFactory::getInstance()->executeQuery($query, $params);
    }
    
    public static function stats() {
        return DBConnectionFactory::getInstance()->getConnectionStats();
    }
}

?>