<?php

namespace SrvKit\Auth\Exceptions;

/**
 * Error Codes
 */
class ErrorCodes
{
    // Authentication
    public const E1001_AUTH_INVALID_CREDENTIALS    = 1001;
    public const E1002_AUTH_UNAUTHORIZED           = 1002;
    public const E1003_AUTH_TOKEN_EXPIRED          = 1003;
    public const E1004_AUTH_TOKEN_INVALID          = 1004;

    // Validation
    public const E2001_VALIDATION_FAILED           = 2001;
    public const E2002_VALIDATION_MISSING_FIELD    = 2002;

    // Request / Content
    public const E3001_BAD_REQUEST                 = 3001;
    public const E3002_CONTENT_NOT_ALLOWED         = 3002;
    public const E3003_UNSUPPORTED_MEDIA_TYPE      = 3003;
    public const E3004_METHOD_NOT_ALLOWED          = 3004;
    public const E3005_INVALID_QUERY_PARAM         = 3005;

    // Resource
    public const E4001_RESOURCE_NOT_FOUND          = 4001;
    public const E4002_RESOURCE_CONFLICT           = 4002;
    public const E4003_RESOURCE_GONE               = 4003;

    // Server/Internal
    public const E5001_INTERNAL_SERVER_ERROR       = 5001;
    public const E5002_SERVICE_UNAVAILABLE         = 5002;

    // Network/API
    public const E6001_GATEWAY_TIMEOUT             = 6001;
    public const E6002_BAD_GATEWAY                 = 6002;

    // Database
    public const E7001_DATABASE_SAVE_FAILED        = 7001;
    public const E7002_DATABASE_UPDATE_FAILED      = 7002;
    public const E7003_DATABASE_DELETE_FAILED      = 7003;
    public const E7004_DATABASE_QUERY_FAILED       = 7004;
    public const E7005_DATABASE_CONNECTION_FAILED  = 7005;

    // Rate Limiting
    public const E8001_RATE_LIMIT_EXCEEDED         = 8001;
    public const E8002_RATE_LIMIT_INVALID_TOKEN    = 8002;

    // Authorization
    public const E9001_AUTHORIZATION_DENIED        = 9001;
    public const E9002_AUTHORIZATION_SCOPE_INVALID = 9002;

    // File Operations
    public const E10001_FILE_UPLOAD_FAILED         = 10001;
    public const E10002_FILE_NOT_FOUND             = 10002;

    // Third-Party Service
    public const E11001_THIRD_PARTY_SERVICE_FAILED = 11001;
    public const E11002_THIRD_PARTY_TIMEOUT        = 11002;

    // Default/Fallback
    public const E9999_UNKNOWN_ERROR               = 9999;
}