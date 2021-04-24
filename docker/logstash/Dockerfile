#FROM docker.elastic.co/logstash/logstash-oss:7.9.1
#RUN logstash-plugin install logstash-input-beats
#USER root
#RUN mkdir -p /home/logstash/logfile
#RUN chown -R logstash:logstash /home/logstash/logfile/

ARG ELK_VERSION
FROM docker.elastic.co/logstash/logstash-oss:${ELK_VERSION}
RUN logstash-plugin install logstash-input-beats
USER root
RUN mkdir -p /home/logstash/logfile
RUN chown -R logstash:logstash /home/logstash/logfile/