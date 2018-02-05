#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <regex.h>

#include "utils.h"


/* Compile the regular expression described by "regex_text" into
   "r". */
static int compile_regex(regex_t * r, const char * regex_text)
{
    int status = regcomp (r, regex_text, REG_EXTENDED|REG_NEWLINE);
    if (status != 0) {
    char error_message[MAX_ERROR_MSG];
    regerror (status, r, error_message, MAX_ERROR_MSG);
        printf("Regex error compiling '%s': %s\n",
                 regex_text, error_message);
        return 1;
    }
    return 0;
}

/*
  Match the string in "to_match" against the compiled regular
  expression in "r".
 */
static int match_regex_once(regex_t * regex, const char * to_match) {
    char regex_error[MAX_ERROR_MSG];
    int result = regexec(regex, to_match, 0, NULL, 0);
    if(!result) {
        printf("Regular expression matched...");
        return REGEX_MATCH_SUCCESS;
    } else {
        if(result == REG_NOMATCH) {
            printf("No match found ...");
            return REGEX_NO_MATCH;
        }
        else {
            regerror(result, regex, regex_error, sizeof(regex_error));
            fprintf(stderr, "Regex match failed: %s\n", regex_error);
            return REGEX_MATCH_SUCCESS;
        }
    }
    return 1;
}


int validate_mac_address(char *mac_address) {
    regex_t regex;
    const char * mac_address_regex =
        "([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})";
    
    compile_regex(&regex, mac_address_regex);
    int regex_result = match_regex_once(&regex, mac_address);
    regfree (&regex);
    if (regex_result == REGEX_MATCH_SUCCESS)
        return REGEX_MATCH_SUCCESS;
    return REGEX_NO_MATCH;
}
/*
int main() {
    char * mac_address = "3D:F2:C9:A6:B3:4a";
    validate_mac_address(mac_address);
    return 0;
}*/
