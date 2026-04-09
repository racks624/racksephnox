<?php

/**
 * @OA\Info(
 *     title="Racksephnox Divine Crypto API",
 *     version="2.0.0",
 *     description="Industrial‑grade cryptocurrency investment platform with RX Machine Series, Trading, and 8888 Hz Wealth Frequency",
 *     @OA\Contact(
 *         email="api@racksephnox.com",
 *         name="Racksephnox API Support"
 *     ),
 *     @OA\License(
 *         name="Proprietary",
 *         url="https://racksephnox.com/license"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token from login endpoint"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints - Register, Login, Logout"
 * )
 * 
 * @OA\Tag(
 *     name="Machines",
 *     description="RX Machine Series (RX1-RX6) - Golden Ratio Φ investment portals with VIP tiers"
 * )
 * 
 * @OA\Tag(
 *     name="Trading",
 *     description="BTC/KES Trading - Market and limit orders, order history"
 * )
 * 
 * @OA\Tag(
 *     name="Wallet",
 *     description="Wallet management - Balance, transfers, transactions"
 * )
 * 
 * @OA\Tag(
 *     name="Transactions",
 *     description="Transaction history and exports"
 * )
 * 
 * @OA\Tag(
 *     name="Referrals",
 *     description="Referral program - Stats, leaderboard, bonuses"
 * )
 * 
 * @OA\Tag(
 *     name="KYC",
 *     description="Know Your Customer - Document upload and verification"
 * )
 */
class SwaggerAnnotations {}
