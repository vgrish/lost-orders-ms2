<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/lost-orders-ms2" package for LostOrdersMS2
 * @see https://github.com/vgrish/lost-orders-ms2
 */

namespace Vgrish\LostOrders\MS2;

class ProcessorManager
{
    protected static \modX $modx;
    protected $response;
    protected ?bool $error = false;
    protected array $output = [];

    public function __construct(?App $app, array $options)
    {
        if (null === $app) {
            $app = App::getInstance();
        }

        $modx = $app::modx();

        if (!isset($modx->lexicon)) {
            $modx->getService('lexicon', 'modLexicon');
        }

        if (!isset($modx->error)) {
            $modx->getService('error', 'error.modError');
        }

        self::$modx = $modx;

        $this->response = $this->run($options);
        $this->processResponse($this->response);
    }

    public static function modx(): \modX
    {
        return self::$modx;
    }

    public function getResponse(): ?\modProcessorResponse
    {
        return $this->response;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function hasError(): bool
    {
        return $this->error;
    }

    public function run($options = [])
    {
        $modx = self::modx();

        if (!$modx->loadClass('modProcessor', '', false, true)) {
            $modx->log(\modX::LOG_LEVEL_ERROR, 'Could not load modProcessor class.');

            return null;
        }

        $action = $options['action'] ?? '';

        if (empty($action)) {
            return null;
        }

        /* calculate processor file path from options and action */
        $isClass = true;
        $processorsPath = $options['processors_path'] ?? '';

        if (isset($options['location']) && !empty($options['location'])) {
            $processorsPath .= \ltrim($options['location'], '/') . '/';
        }

        // Prevent path traversal through the action
        $action = \preg_replace('/[\.]{2,}/', '', \htmlspecialchars($action));
        // Find the processor file, preferring class based processors over old-style processors
        $processorFile = $processorsPath . \ltrim($action . '.php', '/');

        if (!\file_exists($processorFile)) {
            $modx->log(
                \modX::LOG_LEVEL_ERROR,
                "Processor {$processorFile} does not exist; " . \print_r($options, true),
            );

            return null;
        }

        // Prepare a response
        $response = '';

        $modx->error->reset();

        /* ensure processor file is only included once if run multiple times in a request */
        if (!\array_key_exists($processorFile, $modx->processors)) {
            $className = include_once $processorFile;
            $modx->processors[$processorFile] = $className;
        } else {
            $className = $modx->processors[$processorFile];
        }

        // $modx->log(1, var_export($modx->map['msOrder'],true));

        if (!empty($className)) {
            $processor = \call_user_func_array([$className, 'getInstance'], [&$modx, $className, $options]);
        }

        if (empty($processor)) {
            $modx->log(
                \modX::LOG_LEVEL_ERROR,
                "Processor {$processorFile} does not exist; " . \print_r($options, true),
            );

            return null;
        }

        $processor->setPath($processorFile);
        $response = $processor->run();

        return $response;
    }

    protected function processResponse($response): void
    {
        if ($response instanceof \modProcessorResponse) {
            $output = $response->getResponse();

            if (!\is_array($output)) {
                $output = \json_decode($output, true);
            }

            $error = $response->isError();
        } else {
            $message = $response;

            if (empty($message)) {
                $message = 'err_unknown';
            }

            $error = true;
            $output = [
                'success' => empty($error),
                'message' => $message,
            ];
        }

        if (empty($output['data'])) {
            $output['data'] = [];
        }

        if (isset($output['errors']) && empty($output['data'])) {
            $output['data'] = $output['errors'];
            unset($output['errors']);
        }

        $this->error = $error;
        $this->output = $output;
    }
}
