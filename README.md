
# CourseCompass

**CourseCompass** is a personalized course recommendation platform that helps learners discover relevant certification courses based on their interests, goals, and preferences. It integrates courses from popular platforms like Coursera and Udemy, and offers an intuitive quiz-based onboarding process to generate tailored suggestions.


## Tech Stack

- **Frontend**: HTML, CSS, JavaScript 
- **Backend**: PHP
- **Database**: MySQL 
- **Other**: AJAX,Password Hashing (bcrypt)

##  How the Recommendation System Works

1. User fills out a quiz with checkbox-based questions.
2. Each option has associated tag weights.
3. Total tag weights are summed up per user.
4. Courses are scored based on overlapping tags and tag weights.
5. Top N courses are recommended and stored in the `recommendations` table.


## Contributing

Contributions are welcome! If you have suggestions for improvements or new features, please feel free to submit a pull request.

