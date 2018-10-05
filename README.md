# Bank Service Center
To run this program type in console bin/console app:import-csv-file --location "Path/to/file"

# Situation:
Customers can come in to the service center and make a deposit or cash out. Several currencies are supported. There are also certain commissions, both for deposits and withdrawals.

# Commissions

# Money deposit
Commission fee - 0.3% of the amount, but not more than 5.00 EUR

# Cash withdrawal
Different commissions apply to natural and legal persons.

# Commission for individual people
Ordinary commission - 0.3% of the amount.
1000.00 EUR per week (Monday to Sunday) can be taken out for free.
If the amount is exceeded - the commission is calculated based on the amount exceeded (i.e., EUR 1000 is still valid without commission).
This discount only applies to the first 3 withdrawal operations per week - if 4 or more widrawals are conducted, the commission for these operations is calculated as usual - the rule relating to the 1000 EUR is only valid for the first three withdrawals.

# Commission for legal people
Commission fee - 0,3% of the amount, but not less than 0,50 EUR.

# Commission fee currency
The commission fee is always calculated in the currency in which the transaction is performed (for example, if the transaction is performed in USD, the commission  is calculated in USD currency).

# Rounding up
After calculating the commission, it is rounded up to the nearest half of the smallest currency unit
(e.g if the commission fee is 0.023 EUR we round it up to be 0.03 EUR.)

Rounding up is done after the conversion.

# Supported currencies
Currently 3 currencies are supported : EUR, USD and JPY
Conversion curses : 
`EUR:USD` - `1:1.1497`, `EUR:JPY` - `1:129.53`

# Input data
The input data is taken from a CSV file. The file contains executed operations.
Each line contains the folowing data:
- Operation date, format `Y-m-d`
- User id
- user type,`natural` (individual people) arba `legal` (legal people)
- operation type, `cash_in` (money deposit) arba `cash_out` (cash withdrawal)
- operation sum (i.e `2.12` or `3`)
- operation currency, one of `EUR`, `USD`, `JPY`
All of the operations are ordered by date, but they can span up to a couple of years

The program prints the result in the console
Result - calculated commission fee without it's currency

# Example data

```
➜  cat input.csv 
2014-12-31,4,natural,cash_out,1200.00,EUR
2015-01-01,4,natural,cash_out,1000.00,EUR
2016-01-05,4,natural,cash_out,1000.00,EUR
2016-01-05,1,natural,cash_in,200.00,EUR
2016-01-06,2,legal,cash_out,300.00,EUR
2016-01-06,1,natural,cash_out,30000,JPY
2016-01-07,1,natural,cash_out,1000.00,EUR
2016-01-07,1,natural,cash_out,100.00,USD
2016-01-10,1,natural,cash_out,100.00,EUR
2016-01-10,2,legal,cash_in,1000000.00,EUR
2016-01-10,3,natural,cash_out,1000.00,EUR
2016-02-15,1,natural,cash_out,300.00,EUR
2016-02-19,2,natural,cash_out,3000000,JPY
➜  php script.php input.csv
0.6
3
0
0.06
0.9
0
0.7
0.3
0.3
5
0
0
8612
```
