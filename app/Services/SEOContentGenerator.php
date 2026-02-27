<?php

namespace App\Services;

use App\Models\Indicator;
use App\Models\IndicatorData;

class SEOContentGenerator
{
    /**
     * Generate all SEO content for an indicator.
     * Returns an array suitable for upserting into IndicatorSEO.
     */
    public static function generate(Indicator $indicator): array
    {
        $indicator = IndicatorService::queryIndicatorFilters($indicator);

        return [
            'meta_description' => self::generateMetaDescription($indicator),
            'faq'              => self::generateFAQ($indicator),
        ];
        
    }

    /**
     * 
     * Generates a meta description that highlights the indicator name, geographic coverage, and timeframes.
     * 
     */

    protected static function generateMetaDescription(Indicator $indicator): string
    {
        $name         = $indicator->name;
        $locationLine = self::buildLocationLine($indicator);
        $timeframeLine = self::buildTimeframeLine($indicator);

        $parts = ["Explore {$name} data across {$locationLine}."];

        if ($timeframeLine) {
            $parts[] = "Data available {$timeframeLine}.";
        }

        $parts[] = "Powered by Keeping Track Online.";

        return implode(' ', $parts);
    }

    /**
     * 
     * Generates FAQ entries based on available data for the indicator.
     * 
     */
   

    protected static function generateFAQ(Indicator $indicator): array
    {
        $faqs = [];

        // Q1: Always add a citywide question if NYC overall data exists
        $citywideValue = self::getCitywideValue($indicator);
        if ($citywideValue !== null) {
            $faqs[] = [
                'question' => "What is the {$indicator->name} in New York City?",
                'answer'   => self::formatCitywideAnswer($indicator, $citywideValue),
            ];
        }

        // Q2: Geographic variation question — if more than one location exists
        $geoQuestion = self::buildGeoVariationFAQ($indicator);
        if ($geoQuestion) {
            $faqs[] = $geoQuestion;
        }

        // Q3: Trend question — if multiple timeframes exist
        $trendQuestion = self::buildTrendFAQ($indicator);
        if ($trendQuestion) {
            $faqs[] = $trendQuestion;
        }

        // Q4: Breakdown question — if breakdowns exist beyond a default/total
        $breakdownQuestion = self::buildBreakdownFAQ($indicator);
        if ($breakdownQuestion) {
            $faqs[] = $breakdownQuestion;
        }

        return $faqs;
    }

    // -------------------------------------------------------------------------
    // FAQ Builders
    // -------------------------------------------------------------------------

    protected static function buildGeoVariationFAQ(Indicator $indicator): ?array
    {
        $locationTypes = $indicator->filters['location_type'];

        // Prefer boroughs, fall back to first available non-citywide type
        $locationType = $locationTypes->firstWhere('name', 'Borough')
            ?? $locationTypes->first(fn($lt) => $lt->name !== 'New York City');

        if (!$locationType) {
            return null;
        }

        $pluralName = $locationType->plural_name;
        $count      = collect($locationType->locations)->count();
        $countPhrase = $count > 1 ? "NYC's {$count} {$pluralName}" : $pluralName;

        $highest = self::getLocationWithHighestValue($indicator, $locationType->id);

        $answer = "The {$indicator->name} varies across {$countPhrase}.";
        if ($highest) {
            $formattedValue = $highest['value'] ? self::formatValue($highest['value'], $indicator) : null;
            $answer .= " {$highest['name']} has the highest recorded value" 
                . ($formattedValue ? " at {$formattedValue}" : "") . ".";
        }

        return [
            'question' => "How does {$indicator->name} vary across {$countPhrase}?",
            'answer'   => $answer,
        ];
    }

    protected static function buildTrendFAQ(Indicator $indicator): ?array
    {
        $timeframes = $indicator->filters['timeframe'];

        if ($timeframes->count() < 2) {
            return null;
        }

        $earliest = $timeframes->min();
        $latest   = $timeframes->max();

        return [
            'question' => "How has {$indicator->name} changed over time in New York City?",
            'answer'   => "Data for {$indicator->name} is available from {$earliest} to {$latest}. "
                        . "Use Keeping Track Online to explore trends across this period.",
        ];
    }

    protected static function buildBreakdownFAQ(Indicator $indicator): ?array
    {
        $breakdowns = $indicator->filters['breakdown'];

        // Filter out anything that looks like a total/overall breakdown
        $meaningful = collect($breakdowns)->filter(
            fn($b) => !str_contains(strtolower($b->name), 'total') 
                   && !str_contains(strtolower($b->name), 'overall')
                   && !str_contains(strtolower($b->name), 'all ')
        );

        if ($meaningful->isEmpty()) {
            return null;
        }

        $breakdownNames = $meaningful->take(3)->pluck('name')->join(', ', ', and ');

        return [
            'question' => "Can I view {$indicator->name} data broken down by different categories?",
            'answer'   => "Yes. {$indicator->name} data on Keeping Track Online is available broken down by {$breakdownNames}, among others.",
        ];
    }

    // -------------------------------------------------------------------------
    // Data Helpers
    // -------------------------------------------------------------------------

    protected static function getCitywideValue(Indicator $indicator): ?array
    {
        $citywideType = $indicator->filters['location_type']
            ->firstWhere('name', 'New York City');

        if (!$citywideType) {
            return null;
        }

        $citywideLocation = collect($citywideType->locations)->first();

        if (!$citywideLocation) {
            return null;
        }

        $latest = IndicatorData::where('indicator_id', $indicator->id)
            ->where('location_id', $citywideLocation->id)
            ->orderByDesc('timeframe')
            ->first(['value', 'timeframe']);

        return $latest ? ['value' => $latest->value, 'timeframe' => $latest->timeframe] : null;
    }

    protected static function getLocationWithHighestValue(Indicator $indicator, int $locationTypeId): ?array
    {
        $result = IndicatorData::where('indicator_id', $indicator->id)
            ->whereHas('location', fn($q) => $q->where('location_type_id', $locationTypeId))
            ->orderByDesc('value')
            ->with('location:id,name')
            ->first(['value', 'location_id']);

        if (!$result) {
            return null;
        }

        return [
            'name'  => $result->location->name,
            'value' => $result->value,
        ];
    }

    protected static function formatCitywideAnswer(Indicator $indicator, array $data): string
    {
        $value     = self::formatValue($data['value'], $indicator);
        $timeframe = $data['timeframe'];

        return "According to the most recent data ({$timeframe}), the {$indicator->name} in New York City is {$value}. "
             . "Explore breakdowns by borough, community district, and more on Keeping Track Online.";
    }

    protected static function formatValue(mixed $value, Indicator $indicator): string
    {
        if ($value === null) return '';

        $formats = collect($indicator->filters['format'])->pluck('name');

        $format = match(true) {
            $formats->contains('Percent') => 'Percent',
            $formats->contains('Dollar')  => 'Dollar',
            $formats->contains('Number')  => 'Number',
            $formats->contains('Rate')    => 'Rate',
            default                       => null,
        };

        return match($format) {
            'Percent' => number_format($value, 1) . '%',
            'Dollar'  => '$' . number_format($value, 2, '.',','),
            'Number'  => number_format($value, 0),
            'Rate'    => number_format($value, 1),
        };
    }

    protected static function buildLocationLine(Indicator $indicator): string
    {
        $locationTypes = $indicator->filters['location_type'];

        if ($locationTypes->isEmpty()) {
            return 'New York City';
        }

        $names = $locationTypes->pluck('plural_name')->filter()->take(3);

        return $names->join(', ', ', and ');
    }

    protected static function buildTimeframeLine(Indicator $indicator): ?string
    {
        $timeframes = $indicator->filters['timeframe'];

        if ($timeframes->isEmpty()) {
            return null;
        }

        $earliest = $timeframes->min();
        $latest   = $timeframes->max();

        return $earliest === $latest ? "for {$earliest}" : "from {$earliest} to {$latest}";
    }
}