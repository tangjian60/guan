<?php

class Redismanager
{

    private static $iredis = null;

    private static $CACHE_KEY = NULL;

    function __construct()
    {
        $this->connect();
    }

    public function connect()
    {

        if (!is_object(self::$iredis) && !self::$iredis) {
            self::$iredis = new Redis();
            self::$iredis->connect(REDIS_SERVER, REDIS_PORT, REDIS_TIME_OUT);
        }
    }

    public function assignCacheKey($sKey)
    {
        self::$CACHE_KEY = $sKey;
    }

    //====================================
    //=============== STRING
    //====================================
    public function sSet($sKey, $aVal, $timeout = 0)
    {
        self::$iredis->set($sKey, json_encode($aVal), $timeout);
    }

    public function sGet($sKey)
    {
        return self::$iredis->get(self::$CACHE_KEY, $sKey);
    }

    public function expire($sKey, $ttl)
    {
        self::$iredis->expire($sKey, $ttl);
    }

    public function del($sKey)
    {
        self::$iredis->del($sKey);
    }

    //====================================
    //=============== HASH
    //====================================

    public function hashSet($sHashKey, $aVal)
    {
        self::$iredis->hSet(self::$CACHE_KEY, $sHashKey, json_encode($aVal));
    }

    public function hashGet($sHashKey)
    {
        return self::$iredis->hGet(self::$CACHE_KEY, $sHashKey);
    }

    public function hashGetAll()
    {
        return self::$iredis->hGetAll(self::$CACHE_KEY);
    }

    public function hashMGet($aHashKeys)
    {
        return self::$iredis->hMGet(self::$CACHE_KEY, $aHashKeys);
    }

    public function hashExists($sHashKey)
    {
        return self::$iredis->hExists(self::$CACHE_KEY, $sHashKey);
    }

    public function hashDel($sHashKey)
    {
        return self::$iredis->hDel(self::$CACHE_KEY, $sHashKey);
    }


}