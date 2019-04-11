<?php

namespace Shopware\Core\Framework\Workflow\Action;

use GuzzleHttp\Client;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\Validator;
use Shopware\Core\Framework\Twig\StringTemplateRenderer;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotBlankValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SlackAction implements ActionInterface
{

    /**
     * @var StringTemplateRenderer
     */
    private $stringTemplateRenderer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(StringTemplateRenderer $stringTemplateRenderer, ValidatorInterface $validator)
    {
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->validator              = $validator;
    }


    public function getHandlerIdentifier(): string
    {
        return 'sw-slack';
    }

    public function execute(SalesChannelContext $context, array $configuration): void
    {
        $this->validate($configuration);

        $text = $this->stringTemplateRenderer->render($configuration['text'], $context->toArray());

        $client = new Client();
        $client->post(
            $configuration['slackWebHook'],
            [
                'json' => [
                    'text' => $text,
                ]
            ]
        );
    }

    private function validate(array $configuration): void
    {
        $this->validator->validate(
            $configuration['slackWebHook'],
            [
                new NotBlank()
            ]
        );

        $this->validator->validate(
            $configuration['text'],
            [
                new NotBlank()
            ]
        );
    }

}