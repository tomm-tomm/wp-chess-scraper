# This is Chess Scraper, WP plugin for scraping chess data from websites such as www.chess-results.com, www.fide.ratings.com and www.chess.sk. Not recommended to use it as there is an improved WP plugin that uses the API instead of scraping tool. This one is used as an example of using the Simple HTML DOM library for scraping in WordPress.

# Chess Scraper is an experimental WordPress plugin that allows you to get:
# - a player roster for a chess club + information about these players (such as ELO),
# - league team rosters (a club can have several league teams) + information about team players, league table, schedule and results of league matches.
# Data update is done manually only due to longer data retrieval time. Club and team data are retrieved independently of each other.
# The plugin supports only English language localization.
# Notes:
# 1a. Scraping and displaying the requested club roster. The data is obtained based on the club ID number given on the website from which the data is scraped. Currently it is possible to scrape data only for clubs registered in the Slovak Chess Association. Scraping of club rosters from other countries can be programmed. Source used: chess.sk.
# 1b. Along with the club roster, the data about the players from the roster is downloaded. The data is obtained from the players' profile pages on ratings.fide.com.
# 2a. Scraping and displaying of the team rosters. The player data, league table, roster and results of the league matches are downloaded along with the team roster. The data is retrieved based on the team name and league ID number, which are given on the chess-results.com page (from which the data is scraped).

# Plugin enhancement options:
# - Convert scraping to an API connection (if the site provides it).
# - Add manual setting of the number of league teams to the form.
# - Add additional language localizations.
# - Add dropping of database tables after uninstalling the plugin.

# Plugin expansion options:
# - Creation of club, team or player statistics.