<?php

namespace spawnCore\Custom\Response;

class CacheControlState {

    protected bool $isReusable = true;
    protected bool $needsRevalidation = false;
    protected bool $isPrivateData = false;
    protected int $maxLifetime = 600;


    /**
     * Default Cache-control state: "public, max-age=600"
     * @param bool $isReusable
     * @param bool $needsRevalidation
     * @param bool $isPrivateData
     * @param int $maxLifetime
     */
    public function __construct(
        bool $isReusable = true,
        bool $needsRevalidation = false,
        bool $isPrivateData = false,
        int $maxLifetime = 600
    )
    {
        if(MODE === 'dev') {
            //disable all caching
            $this->isReusable = false;
            $this->needsRevalidation = true;
            $this->isPrivateData = true;
            $this->maxLifetime = 60;
        }
        else {
            $this->isReusable = $isReusable;
            $this->needsRevalidation = $needsRevalidation;
            $this->isPrivateData = $isPrivateData;
            $this->maxLifetime = $maxLifetime;
        }
    }


    public function setReusable(bool $reusable = true): self {
        $this->isReusable = $reusable;
        return $this;
    }

    public function setNeedsRevalidation(bool $needsRevalidation = true): self {
        $this->needsRevalidation = $needsRevalidation;
        return $this;
    }

    public function setIsPrivateData(bool $isPrivate = false): self {
        $this->isPrivateData = $isPrivate;
        return $this;
    }

    public function setMaxLifetime(int $maxLifetime = 600): self {
        $this->maxLifetime = $maxLifetime;
        return $this;
    }


    public function getCacheControlValue(): string {
        $values = [];

        if(!$this->isReusable /*|| MODE == 'dev'*/) {
            return 'private,no-cache,no-store';
        }


        $values[] = $this->isPrivateData ? 'private' : 'public';

        if($this->needsRevalidation) {
            $values[] = 'no-cache';
        }

        if($this->maxLifetime) {
            $values[] = 'max-age='.$this->maxLifetime;
        }


        return implode(', ', $values);
    }






}