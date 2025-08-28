<?php
// Gann Square of Nine Calculator
// Created for GitHub Project
// Date: 2025-08-29

// Define variables and set default values
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$calculated = false;
$supportLevels = [];
$resistanceLevels = [];
$recommendation = [];

// Calculate Gann levels if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $price > 0) {
    $calculated = true;
    
    // Calculate Gann levels
    $sqrtPrice = sqrt($price);
    $degrees = [0, 45, 90, 135, 180, 225, 270, 315, 360];
    
    foreach ($degrees as $degree) {
        // Calculate support and resistance using Gann's formula
        $factor = $degree / 360;
        $adjustedSqrt = $sqrtPrice + $factor;
        $gannValue = pow($adjustedSqrt, 2);
        
        // Round to appropriate decimal places
        $gannValue = round($gannValue, 2);
        
        if ($degree < 180) {
            $supportLevels[] = $gannValue;
        } else {
            $resistanceLevels[] = $gannValue;
        }
    }
    
    // Sort and get unique values
    sort($supportLevels);
    sort($resistanceLevels);
    $supportLevels = array_unique($supportLevels);
    $resistanceLevels = array_unique($resistanceLevels);
    
    // Generate trading recommendation
    $buyPrice = $supportLevels[0] ?? $price;
    $sellPrice = $resistanceLevels[0] ?? $price;
    
    $recommendation = [
        'buy' => [
            'price' => $buyPrice,
            'targets' => array_slice($resistanceLevels, 0, 4),
            'stoploss' => min($supportLevels) * 0.99
        ],
        'sell' => [
            'price' => $sellPrice,
            'targets' => array_slice($supportLevels, 0, 4),
            'stoploss' => max($resistanceLevels) * 1.01
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Free online Gann Square of Nine calculator for technical analysis and intraday trading. Calculate support and resistance levels based on WD Gann's mathematical trading principles.">
    <meta name="keywords" content="gann square of nine, gann calculator, technical analysis, trading tool, support resistance, intraday trading, stock market calculator">
    <title>Gann Square of Nine Calculator | Technical Analysis Tool</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #4b5563;
            --light: #f3f4f6;
            --lighter: #f9fafb;
            --dark: #1f2937;
            --success: #10b981;
            --danger: #ef4444;
            --border: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--lighter);
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px 0;
        }
        
        h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 1.2rem;
            color: var(--secondary);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .calculator {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary);
        }
        
        input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input[type="number"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: var(--primary-dark);
        }
        
        .results {
            display: <?php echo $calculated ? 'grid' : 'none'; ?>;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .result-box {
            background-color: var(--lighter);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .result-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary);
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        
        .levels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .level {
            padding: 10px;
            background: white;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .support {
            color: var(--success);
        }
        
        .resistance {
            color: var(--danger);
        }
        
        .recommendation {
            grid-column: 1 / -1;
            margin-top: 20px;
        }
        
        .recommendation-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        
        .buy, .sell {
            padding: 20px;
            border-radius: 8px;
        }
        
        .buy {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
        }
        
        .sell {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
        }
        
        .rec-title {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .buy .rec-title {
            color: var(--success);
        }
        
        .sell .rec-title {
            color: var(--danger);
        }
        
        .targets {
            margin-top: 10px;
        }
        
        .target {
            display: inline-block;
            padding: 5px 10px;
            background: white;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .info-section {
            margin-bottom: 40px;
        }
        
        .info-section h2 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        
        .info-section h3 {
            font-size: 1.3rem;
            color: var(--secondary);
            margin: 25px 0 15px;
        }
        
        .info-section p {
            margin-bottom: 15px;
        }
        
        .faq-item {
            margin-bottom: 20px;
        }
        
        .faq-question {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .results, .recommendation-box, .levels {
                grid-template-columns: 1fr;
            }
            
            .calculator {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gann Square of Nine Calculator</h1>
            <p class="subtitle">Calculate key support and resistance levels for intraday trading using W.D. Gann's mathematical trading principles</p>
        </header>
        
        <section class="calculator">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="price">Enter Current Market Price:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" placeholder="Enter price value" value="<?php echo $price > 0 ? $price : ''; ?>" required>
                </div>
                <button type="submit">Calculate Gann Levels</button>
            </form>
            
            <div class="results" id="results">
                <div class="result-box">
                    <div class="result-title">Support Levels</div>
                    <div class="levels">
                        <?php
                        if ($calculated && !empty($supportLevels)) {
                            foreach ($supportLevels as $level) {
                                echo '<div class="level support">' . $level . '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <div class="result-box">
                    <div class="result-title">Resistance Levels</div>
                    <div class="levels">
                        <?php
                        if ($calculated && !empty($resistanceLevels)) {
                            foreach ($resistanceLevels as $level) {
                                echo '<div class="level resistance">' . $level . '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <?php if ($calculated && !empty($recommendation)): ?>
                <div class="recommendation">
                    <div class="result-title">Trading Recommendation</div>
                    <div class="recommendation-box">
                        <div class="buy">
                            <div class="rec-title">Buy Recommendation</div>
                            <div><strong>Entry:</strong> <?php echo $recommendation['buy']['price']; ?></div>
                            <div><strong>Stop Loss:</strong> <?php echo round($recommendation['buy']['stoploss'], 2); ?></div>
                            <div class="targets">
                                <strong>Targets:</strong><br>
                                <?php
                                foreach ($recommendation['buy']['targets'] as $target) {
                                    echo '<span class="target">' . $target . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="sell">
                            <div class="rec-title">Sell Recommendation</div>
                            <div><strong>Entry:</strong> <?php echo $recommendation['sell']['price']; ?></div>
                            <div><strong>Stop Loss:</strong> <?php echo round($recommendation['sell']['stoploss'], 2); ?></div>
                            <div class="targets">
                                <strong>Targets:</strong><br>
                                <?php
                                foreach ($recommendation['sell']['targets'] as $target) {
                                    echo '<span class="target">' . $target . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <section class="info-section">
            <h2>About Gann Square of Nine</h2>
            <p>The Gann Square of Nine is a technical analysis tool developed by W.D. Gann, a famous financial trader. This mathematical tool is based on the principle that markets move in predictable patterns and cycles, which can be identified using geometric and numerical relationships :cite[1]:cite[8].</p>
            
            <p>The Square of Nine gets its name from its structure: it's essentially a spiral of numbers starting from 1 at the center, with consecutive numbers placed in a clockwise spiral pattern. Key angles on this spiral (45°, 90°, 135°, 180°, 225°, 270°, 315°, and 360°) are believed to represent significant support and resistance levels in financial markets :cite[8].</p>
            
            <p>Traders use the Gann Square of Nine to predict potential support and resistance levels, identify price targets, and determine optimal entry and exit points for trades. It's particularly popular among intraday traders for its ability to generate real-time trading signals :cite[1].</p>
        </section>
        
        <section class="info-section">
            <h2>How to Use the Gann Square of Nine Calculator</h2>
            
            <h3>Step 1: Enter the Current Market Price</h3>
            <p>Input the current market price of the stock, index, or commodity you want to analyze. For best results, use the average price or the Weighted Average Price (WAP) after the first 15-60 minutes of trading :cite[1].</p>
            
            <h3>Step 2: Click Calculate</h3>
            <p>Press the "Calculate Gann Levels" button to process the price through the Gann Square of Nine algorithm. The calculator will generate key support and resistance levels based on mathematical relationships derived from your input price.</p>
            
            <h3>Step 3: Analyze the Results</h3>
            <p>The calculator will display:</p>
            <ul>
                <li><strong>Support Levels:</strong> Price levels where buying interest may emerge</li>
                <li><strong>Resistance Levels:</strong> Price levels where selling pressure may increase</li>
                <li><strong>Trading Recommendations:</strong> Specific buy and sell recommendations with targets and stop loss levels</li>
            </ul>
            
            <h3>Step 4: Apply to Your Trading</h3>
            <p>Use the generated levels to inform your trading decisions:</p>
            <ul>
                <li>Consider buying near support levels with a stop loss below the nearest support</li>
                <li>Consider selling near resistance levels with a stop loss above the nearest resistance</li>
                <li>Set profit targets at the subsequent resistance (for long positions) or support (for short positions) levels</li>
            </ul>
            
            <p>For optimal results, combine Gann levels with other technical analysis tools and risk management principles :cite[8].</p>
        </section>
        
        <section class="info-section">
            <h2>Frequently Asked Questions</h2>
            
            <div class="faq-item">
                <div class="faq-question">What is the best time to use the Gann Square of Nine calculator?</div>
                <p>The ideal time to use the calculator is 15-60 minutes after the market opens, once the price has stabilized and established a meaningful trading range for the day. This helps generate more accurate support and resistance levels :cite[1].</p>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">How accurate is the Gann Square of Nine for trading?</div>
                <p>While no trading tool guarantees 100% accuracy, many traders have found the Gann Square of Nine to be remarkably accurate in identifying potential support and resistance levels. The accuracy depends on proper application and combining it with other technical analysis tools. Historical observation since 2009 has shown "uncanny accuracy for intraday trading" according to some traders :cite[8].</p>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Can I use the Gann calculator for all time frames?</div>
                <p>While the Gann Square of Nine can be applied to various time frames, it's primarily designed for and most effective in intraday trading. The calculator works best when markets are trending and may be less effective in ranging or extremely volatile markets. For position trading, other Gann tools like the Square of 144 might be more appropriate :cite[1]:cite[8].</p>
            </div>
        </section>
    </div>

    <script>
        // Simple JavaScript to enhance user experience
        document.getElementById('price').focus();
        
        // Show results if calculated
        <?php if ($calculated): ?>
            document.getElementById('results').style.display = 'grid';
        <?php endif; ?>
    </script>
</body>
</html>
