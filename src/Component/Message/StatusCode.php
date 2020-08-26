<?php
/**
 * StatusCode.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\Message;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * description
 * @Constants()
 * @method static getMessage(int $code): string
 */
class StatusCode extends AbstractConstants
{
    // constants of the class StatusCode
    /**
     * @Message("continue")
     */
    const CONTINUE = 100;
    /**
     * @Message("switching protocols")
     */
    const SWITCHING_PROTOCOLS = 101;
    /**
     * @Message("processing")
     */
    const PROCESSING = 102;
    /**
     * @Message("ok")
     */
    const OK = 200;
    /**
     * @Message("created")
     */
    const CREATED = 201;
    /**
     * @Message("accepted")
     */
    const ACCEPTED = 202;
    /**
     * @Message("non authoritative information")
     */
    const NON_AUTHORITATIVE_INFORMATION = 203;
    /**
     * @Message("no content")
     */
    const NO_CONTENT = 204;
    /**
     * @Message("reset content")
     */
    const RESET_CONTENT = 205;
    /**
     * @Message("partial content")
     */
    const PARTIAL_CONTENT = 206;
    /**
     * @Message("multi status")
     */
    const MULTI_STATUS = 207;
    /**
     * @Message("already reported")
     */
    const ALREADY_REPORTED = 208;
    /**
     * @Message("im used")
     */
    const IM_USED = 226;
    /**
     * @Message("multiple choices")
     */
    const MULTIPLE_CHOICES = 300;
    /**
     * @Message("moved permanently")
     */
    const MOVED_PERMANENTLY = 301;
    /**
     * @Message("found")
     */
    const FOUND = 302;
    /**
     * @Message("see other")
     */
    const SEE_OTHER = 303;
    /**
     * @Message("not modified")
     */
    const NOT_MODIFIED = 304;
    /**
     * @Message("use proxy")
     */
    const USE_PROXY = 305;
    /**
     * @Message("switch proxy")
     */
    const SWITCH_PROXY = 306;
    /**
     * @Message("temporary redirect")
     */
    const TEMPORARY_REDIRECT = 307;
    /**
     * @Message("permanent redirect")
     */
    const PERMANENT_REDIRECT = 308;
    /**
     * @Message("bad request")
     */
    const BAD_REQUEST = 400;
    /**
     * @Message("unauthorized")
     */
    const UNAUTHORIZED = 401;
    /**
     * @Message("payment required")
     */
    const PAYMENT_REQUIRED = 402;
    /**
     * @Message("forbidden")
     */
    const FORBIDDEN = 403;
    /**
     * @Message("not found")
     */
    const NOT_FOUND = 404;
    /**
     * @Message("method not allowed")
     */
    const METHOD_NOT_ALLOWED = 405;
    /**
     * @Message("not acceptable")
     */
    const NOT_ACCEPTABLE = 406;
    /**
     * @Message("proxy authentication required")
     */
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    /**
     * @Message("request time out")
     */
    const REQUEST_TIME_OUT = 408;
    /**
     * @Message("conflict")
     */
    const CONFLICT = 409;
    /**
     * @Message("gone")
     */
    const GONE = 410;
    /**
     * @Message("length required")
     */
    const LENGTH_REQUIRED = 411;
    /**
     * @Message("precondition failed")
     */
    const PRECONDITION_FAILED = 412;
    /**
     * @Message("request entity too large")
     */
    const REQUEST_ENTITY_TOO_LARGE = 413;
    /**
     * @Message("request uri too large")
     */
    const REQUEST_URI_TOO_LARGE = 414;
    /**
     * @Message("unsupported media type")
     */
    const UNSUPPORTED_MEDIA_TYPE = 415;
    /**
     * @Message("requested range not satisfiable")
     */
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    /**
     * @Message("expectation failed")
     */
    const EXPECTATION_FAILED = 417;
    /**
     * @Message("misdirected request")
     */
    const MISDIRECTED_REQUEST = 421;
    /**
     * @Message("unprocessable entity")
     */
    const UNPROCESSABLE_ENTITY = 422;
    /**
     * @Message("locked")
     */
    const LOCKED = 423;
    /**
     * @Message("failed dependency")
     */
    const FAILED_DEPENDENCY = 424;
    /**
     * @Message("unordered collection")
     */
    const UNORDERED_COLLECTION = 425;
    /**
     * @Message("upgrade required")
     */
    const UPGRADE_REQUIRED = 426;
    /**
     * @Message("precondition required")
     */
    const PRECONDITION_REQUIRED = 428;
    /**
     * @Message("too many requests")
     */
    const TOO_MANY_REQUESTS = 429;
    /**
     * @Message("request header fields too large")
     */
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    /**
     * @Message("unavailable for legal reasons")
     */
    const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    /**
     * @Message("internal server error")
     */
    const INTERNAL_SERVER_ERROR = 500;
    /**
     * @Message("not implemented")
     */
    const NOT_IMPLEMENTED = 501;
    /**
     * @Message("bad gateway")
     */
    const BAD_GATEWAY = 502;
    /**
     * @Message("service unavailable")
     */
    const SERVICE_UNAVAILABLE = 503;
    /**
     * @Message("gateway time out")
     */
    const GATEWAY_TIME_OUT = 504;
    /**
     * @Message("http version not supported")
     */
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    /**
     * @Message("variant also negotiates")
     */
    const VARIANT_ALSO_NEGOTIATES = 506;
    /**
     * @Message("insufficient storage")
     */
    const INSUFFICIENT_STORAGE = 507;
    /**
     * @Message("loop detected")
     */
    const LOOP_DETECTED = 508;
    /**
     * @Message("not extended")
     */
    const NOT_EXTENDED = 510;
    /**
     * @Message("network authentication required")
     */
    const NETWORK_AUTHENTICATION_REQUIRED = 511;
}
