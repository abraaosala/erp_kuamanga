<?php

declare(strict_types=1);

use App\Console\DatabaseExporter;

it('formats null as the NULL literal', function (): void {
    expect(DatabaseExporter::quoteValue(null))->toBe('NULL');
});

it('formats integers without quotes', function (): void {
    expect(DatabaseExporter::quoteValue(42))->toBe('42');
    expect(DatabaseExporter::quoteValue(-7))->toBe('-7');
});

it('formats floats and booleans without quotes', function (): void {
    expect(DatabaseExporter::quoteValue(3.14))->toBe('3.14');
    expect(DatabaseExporter::quoteValue(true))->toBe('1');
    expect(DatabaseExporter::quoteValue(false))->toBe('0');
});

it('quotes strings and escapes quotes and backslashes', function (): void {
    expect(DatabaseExporter::quoteValue("João"))->toBe("'João'");
    expect(DatabaseExporter::quoteValue("O'Reilly"))->toBe("'O''Reilly'");
    expect(DatabaseExporter::quoteValue('a\b'))->toBe("'a\\\\b'");
});

it('hex encodes blob and binary columns', function (): void {
    expect(DatabaseExporter::quoteValue("\x01\x02", 'blob'))->toBe('0x0102');
    expect(DatabaseExporter::quoteValue("abc", 'varbinary(16)'))->toBe('0x616263');
});

it('quotes plain strings that match binary-like names', function (): void {
    expect(DatabaseExporter::quoteValue("só texto"))->toBe("'só texto'");
});
