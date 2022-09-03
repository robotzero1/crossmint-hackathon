## Crossmint Hackathon Entry

Adding the Crossmint Minting API into a 'conventional' checkout flow to create an NFT .

The project combines the APIs of Stripe, Crossmint and Printful to buy an NFT and printed poster in one payment.

![image](https://user-images.githubusercontent.com/60509953/185814159-cab1206e-450d-4d28-93be-e3ceac45daa4.png)

There is an occasional bug with the Stripe payment. Sometimes the mint/print process starts prematurely. A video of the process functioning correctly can be seen here:  https://streamable.com/huczpc

Demo site - https://robot.moda/
Demo card is 4242 4242 4242 4242 04/24 424

## Purchase flow

1)	Customer chooses an image from the poster. Customer enters payment details
2)	Stripe processes the order. When complete Stripe sends a webhook to the server containing the order details
3)	Server webhook page creates a file containing the order data
4)	Web page polls for the existence of this file
5)	When the web page detects this file it simultaneously starts minting and print order process via APIs using the content of this file
6)	Printful API responds when print job is accepted
7)	Crossmint API responds mint in progress
8)	Web page polls for mint complete via ID received in step 7
9)	When API responds mint complete. Customer is show the mint result (TODO)


There are a few things that didn’t make this release but I’ll add them when the competition has finished.

•	Mobile friendly  
~~•	Show 'minted' on mouseover each block for minted items and prevent selection~~  
•	Switch to idempotency version with JS check before mint attempt  
•	Clean up the code  
~~•	Prettier modal launch~~ 
~~•	Close second modal ~~
•	Moar APIs https://www.smarty.com/ address validation  

## Credits

Font - https://medialoot.com/item/kirlian-free-industrial-font/
