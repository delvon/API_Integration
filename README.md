# These are work done using REST API to integrate Moodle with external systems.
# Files in hub folder are files to check if system is available, check if user is enrolled on courses and also check all courses on system. It also creates a user from a submited html form and enrol the user on courses selected in teh html form. This is just removing some code from a client's system that does ecommerce and after purchase it does it's checks and if user is not yet enrolled on the specified course it enrols them if they already exist and if not also create the user tehn enrol them on the course.
# File in medco folder are to send information from moodle to update a client's system using the REST API. It sends specific information that the client requested to their system which is equipped to update those details. The folder also contains an error report file which is what the client's use to see teh last successful transfer, the transfers so far and errors returned for those that have not been transferred successfully.
