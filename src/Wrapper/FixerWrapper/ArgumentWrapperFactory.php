<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ArgumentWrapperFactory
{
    /**
     * @return ArgumentWrapper[]
     */
    public function createArgumentsFromTokensAndFunctionPosition(Tokens $tokens, int $position): array
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$position], [T_VARIABLE], __METHOD__);

        $argumentsBracketStart = $tokens->getNextTokenOfKind($position, ['(']);
        $argumentsBracketEnd = $tokens->findBlockEnd(
            Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
            $argumentsBracketStart
        );

        if ($argumentsBracketStart === ($argumentsBracketEnd + 1)) {
            return [];
        }

        $arguments = [];
        for ($i = $argumentsBracketStart + 1; $i < $argumentsBracketEnd; ++$i) {
            $token = $tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            $arguments[] = $this->createFromTokensAndPosition($tokens, $i);
        }

        return $arguments;
    }

    private function createFromTokensAndPosition(Tokens $tokens, int $position): ArgumentWrapper
    {
        return new ArgumentWrapper($tokens, $position);
    }
}
