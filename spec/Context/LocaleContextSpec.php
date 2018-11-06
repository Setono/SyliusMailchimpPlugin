<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Context;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Context\LocaleContext;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface as BaseLocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class LocaleContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $localeRepository,
        BaseLocaleContextInterface $baseLocaleContext
    ): void {
        $this->beConstructedWith($localeRepository, $baseLocaleContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleContext::class);
    }

    function it_implements_locale_context_interface(): void
    {
        $this->shouldHaveType(LocaleContextInterface::class);
    }

    function it_gets_locales(
        RepositoryInterface $localeRepository,
        BaseLocaleContextInterface $baseLocaleContext,
        LocaleInterface $locale
    ): void {
        $baseLocaleContext->getLocaleCode()->willReturn('test');
        $localeRepository->findOneBy(['code' => 'test'])->willReturn($locale->getWrappedObject());

        $this->getLocale()->shouldBeEqualTo($locale);
    }
}
