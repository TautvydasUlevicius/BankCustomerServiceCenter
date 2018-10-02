# Bank Service Center
# Situation:
Customers can come in to the service center and make a money deposit or cash out some
money. Couple of currencies are supported. And there are certain commissions for certain
operations.
# Commissions
# Money deposit

Commission - 0.03% from the operation amount, but the commission can't be more than
5.00 EUR
# Cash withdrawal
The commission fee is different for natural and legal people

# Commission for individual people
Regular commission - 0.3% from the operation amount.
Per week you can withdrawal 1000.00 EUR for free.
If the operation amount is more than the free amount the commission fee is calculated
from the exceeded amount.
This discount only applies for the first 3 operations that same week. If it's the fourth,
fifth and etc. operation the same week, the commission fee is calculated normally without
any discounts. The 1000 EUR discount only applies for the first 3 operations per week

# Commission for legal people
Commission fee is 0.3% from the operation amount, but it can't be less than 0.50 EUR.

# Commission fee currency
For example: if the operation is made in USD the commission fee must be in USD

# Rounding up
When the commission fee is calculated, it has to be rounded up to the bigger side.
For example, if the commission fee is 0.023 EUR it has to be 0.03 EUR.

We do the rounding up after the commission fee is calculated.

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